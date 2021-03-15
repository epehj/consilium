<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AW\PlansBundle\Form\CommandeDetType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use AW\DoliBundle\Entity\Societe;
use AW\DoliBundle\Repository\SocieteRepository;
use AW\DoliBundle\Repository\UserRepository;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Filesystem\Filesystem;

class CommandeType extends AbstractType
{
  private $tokenStorage;

  public function __construct(TokenStorageInterface $tokenStorage)
  {
    $this->tokenStorage = $tokenStorage;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $user = $this->tokenStorage->getToken()->getUser();
    $builder
      ->add('societe', EntityType::class, array(
        'label' => 'Client',
        'placeholder' => 'Choisir un client',
        'class' => 'AWDoliBundle:Societe',
        'query_builder' => function(SocieteRepository $er) use($user){
          return $er->getClientsQueryBuilder($user);
        },
        'choice_label' => function(Societe $societe){
          return $societe->getBarcode().' - '.$societe->getName();
        }
      ))
      ->add('refClient', TextType::class, array(
        'label' => 'Ref. Client',
        'required' => false
      ))
        // TODO remplacer cette partie par l'addresseType
      ->add('site', TextType::class, array(
        'label' => 'Nom du site',
//          'required' =>true,
      ))
      ->add('address1', TextType::class, array(
        'label' => 'Adresse',
//          'required' => true,
      ))
      ->add('address2', TextType::class, array(
        'label' => 'Adresse complémentaire',
        'required' => false
      ))
      ->add('zip', TextType::class, array(
        'label' => 'Code postal'
      ))
      ->add('town', TextType::class, array(
        'label' => 'Ville'
      ))
//      ->add('releve', ChoiceType::class, array(
//        'label' => 'Relevés plans',
//        'choices' => array(
//          'Oui' => true,
//          'Non' => false
//        )
//      ))
//      ->add('pose', ChoiceType::class, array(
//        'label' => 'Pose plans',
//        'choices' => array(
//          'Oui' => true,
//          'Non' => false
//        )
//      ))
      ->add('remarques', TextareaType::class, array(
        'label' => 'Remarques',
        'required' => false
      ))
      ->add('listDet', CollectionType::class, array(
        'label' => false,
        'entry_type' => CommandeDetType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'by_reference' => false
      ))
      ->add('cgv', CheckboxType::class, array(
        'label' => "J'ai lu les conditions générales de vente et j'y adhère sans réserve.",
        'required' => true,
        'mapped' => false
      ))
    ;

    // see https://symfony.com/doc/current/form/dynamic_form_modification.html
    $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
      $commande = $event->getData();
      $form = $event->getForm();

      // on vérifie que commande existe, dans le cas d'un appel depuis un subform/nestedform et qu'on ait une commandeSTType qui l'ai appelé
        if ($commande) {
            $dir = empty($commande->getDir()) ? tempnam(sys_get_temp_dir(), 'AW') : $commande->getDir();
            $fs = new Filesystem();
            if (!is_dir($dir)) {
                $fs->remove($dir);
            }
            if (!file_exists($dir)) {
                $fs->mkdir($dir);
            }
            if ($commande->getSociete()) {
                if ($commande->getUserContact()) {
                    $userContact = $commande->getUserContact();
                } elseif ($this->tokenStorage->getToken()->getUser()->getSociete()) {
                    $userContact = $this->tokenStorage->getToken()->getUser();
                } else {
                    $userContact = null;
                }

                $form
                    ->add('userContact', EntityType::class, array(
                        'label' => 'Contact',
                        'class' => 'AWDoliBundle:User',
                        'query_builder' => function (UserRepository $er) use ($commande) {
                            return $er->getSocieteUsersQueryBuilder($commande->getSociete());
                        },
                        'choice_label' => function ($user) {
                            return $user->getFullName() . ' - ' . $user->getSociete()->getName() . ' <' . $user->getEmail() . '>';
                        },
                        'choice_attr' => function ($user, $key, $index) {
                            return array(
                                'data-name' => $user->getFullName(),
                                'data-address1' => $user->getAddress1(),
                                'data-address2' => $user->getAddress2(),
                                'data-zip' => $user->getZip(),
                                'data-town' => $user->getTown(),
                                'data-country' => $user->getCountry() ? $user->getCountry()->getId() : 1
                            );
                        },
                        'data' => $userContact,
                        'required' => false
                    ))
                    ->add('dir', HiddenType::class, array(
                        'data' => basename($dir)
                    ))
                    ->add('shippingRecipient', TextType::class, array(
                        'label' => 'Nom',
                        'data' => $commande->getShippingRecipient() ? $commande->getShippingRecipient() : $commande->getSociete()->getName()
                    ))
                    ->add('shippingAddress1', TextType::class, array(
                        'label' => 'Adresse',
                        'data' => $commande->getShippingAddress1() ? $commande->getShippingAddress1() : $commande->getSociete()->getAddress1()
                    ))
                    ->add('shippingAddress2', TextType::class, array(
                        'label' => 'Adresse complémentaire',
                        'data' => $commande->getShippingAddress2() ? $commande->getShippingAddress2() : $commande->getSociete()->getAddress2(),
                        'required' => false
                    ))
                    ->add('shippingZip', TextType::class, array(
                        'label' => 'Code postal',
                        'data' => $commande->getShippingZip() ? $commande->getShippingZip() : $commande->getSociete()->getZip()
                    ))
                    ->add('shippingTown', TextType::class, array(
                        'label' => 'Ville',
                        'data' => $commande->getShippingTown() ? $commande->getShippingTown() : $commande->getSociete()->getTown()
                    ))
                    ->add('shippingCountry', EntityType::class, array(
                        'label' => 'Pays',
                        'class' => 'AWDoliBundle:Country',
                        'choice_label' => 'name',
                        'data' => $commande->getShippingCountry() ? $commande->getShippingCountry() : $commande->getSociete()->getCountry()
                    ));

                // ajout d'un contact BATs
                if ($commande->getSociete()->getInfosPlans() and $commande->getSociete()->getInfosPlans()->getAllowContactBat()) {
                    $form
                        ->add('contactBATName', TextType::class, array(
                            'label' => 'Nom du contact',
                            'required' => false
                        ))
                        ->add('contactBATPhone', TextType::class, array(
                            'label' => 'Téléphone',
                            'required' => false
                        ))
                        ->add('contactBATEmail', TextType::class, array(
                            'label' => 'E-Mail',
                            'required' => false
                        ));
                }
            }
        }
    });
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AW\PlansBundle\Entity\Commande',
      'translation_domain' => false
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'aw_plansbundle_commande';
  }
}
