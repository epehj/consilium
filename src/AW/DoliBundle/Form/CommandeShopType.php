<?php

namespace AW\DoliBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use AW\DoliBundle\Entity\Contact;
use AW\DoliBundle\Entity\Commande;
use AW\DoliBundle\Entity\Societe;
use AW\DoliBundle\Repository\ContactRepository;
use AW\DoliBundle\Repository\SocieteRepository;
use AW\DoliBundle\Service\PricelistUtils;

class CommandeShopType extends AbstractType
{
  private $em;
  private $tokenStorage;
  private $priceList;

  public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, PricelistUtils $priceList)
  {
    $this->em = $em;
    $this->tokenStorage = $tokenStorage;
    $this->priceList = $priceList;
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
        'class' => 'AWDoliBundle:Societe',
        'query_builder' => function(SocieteRepository $er) use($user){
          return $er->getClientsQueryBuilder($user);
        },
        'choice_label' => function(Societe $societe){
          return $societe->getBarcode().' - '.$societe->getName();
        }
      ))
      ->add('refClient', TextType::class, array(
        'label' => 'Votre référence',
        'required' => false
      ))
      ->add('files', FileType::class, array(
        'label' => 'Joindre des fichiers',
        'required' => false,
        'multiple' => true,
        'mapped' => false
      ))
      ->add('cgv', CheckboxType::class, array(
        'label' => "J'ai lu les conditions générales de vente et j'y adhère sans réserve.",
        'required' => true,
        'mapped' => false
      ))
    ;

    $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
      $commande = $event->getData();

      $ch13 = $this
        ->em
        ->getRepository('AWDoliBundle:Product')
        ->findOneByRef('CH13')
      ;
      $ch13price = $this->priceList->getCustomerPrice(1, $ch13, $commande->getSociete());

      $form = $event->getForm();
      $form
        ->add('contactShipping', EntityType::class, array(
          'label' => 'Adresse de livraison',
          'class' => 'AWDoliBundle:Contact',
          'query_builder' => function(ContactRepository $er) use($commande) {
            return $er->getSocieteShippingContactsQueryBuilder($commande->getSociete());
          },
          'choice_label' => function(Contact $contact){
            return $contact->getFullName();
          },
          'choice_attr' => function(Contact $contact, $key, $value){
            return array(
              'data-content' => '<strong>'.$contact->getFullName().'</strong><br>'.nl2br($contact->getAddress()).'<br>'.$contact->getZip().' '.$contact->getTown().'<br>'.$contact->getCountry()->getName()
            );
          },
          'placeholder' => "Votre société",
          'required' => false,
          'attr' => array(
            'class' => 'selectpicker',
            'data-width' => '100%'
          ),
          'mapped' => false
        ))
        ->add('shippingMethod', ChoiceType::class, array(
          'label' => "Méthode d'expédition",
          'choices' => array(
            'Chronopost (+'.number_format($ch13price, 2, ',', ' ').' € HT)' => Commande::SHIPMENT_MODE_CHRONO,
            "À récupérer à l'agence de Chassieu" => Commande::SHIPMENT_MODE_AGENCE
          )
        ))
      ;
    });
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AW\DoliBundle\Entity\Commande',
      'translation_domain' => false
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'aw_dolibundle_commande_shop';
  }
}
