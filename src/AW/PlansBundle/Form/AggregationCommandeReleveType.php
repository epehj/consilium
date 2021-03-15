<?php


namespace AW\PlansBundle\Form;


use AW\DoliBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/** Form qui regroupe toutes les données propres a une commande, que l'on affiche dans la sous commande */
class AggregationCommandeReleveType extends \Symfony\Component\Form\AbstractType
{
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address1', TextType::class, array(
                'label' => 'Adresse'
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
            ->add('contactModification', TextType::class, array(
                'label' => 'Contact',
                'required' => false
            ))
            // si l'on utilise le AddReleveurType, le user affecté est enregistré dans une nouvelle commande
            ->add('releveur', EntityType::class, array(
                'label' => 'Affecter un releveur',
                'class' => 'AWDoliBundle:User',
                'query_builder' => function(UserRepository $er) {
                    return $er->getUsersInReleveurGroupQueryBuilder();
                },
                'choice_label' => function($user){
                    return $user->getFullName().' <'.$user->getEmail().'>';
                }
            ))
            //le field remarques vient de la commande
            ->add('remarques', TextareaType::class, array(
                'label' => 'Remarques',
                'required' => false
            ))
            ;
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
//            $commande = $event->getData();
//            $form = $event->getForm();
//
//            if ($commande->getUserContact()) {
//                $userContact = $commande->getUserContact();
//            } elseif ($this->tokenStorage->getToken()->getUser()->getSociete()) {
//                $userContact = $this->tokenStorage->getToken()->getUser();
//            } else {
//                $userContact = null;
//            }
//
//            $form->add('userContact', EntityType::class, array(
//                'label' => 'Contact',
//                'class' => 'AWDoliBundle:User',
//                'query_builder' => function (UserRepository $er) use ($commande) {
//                    return $er->getSocieteUsersQueryBuilder($commande->getSociete());
//                },
//                'choice_label' => function ($user) {
//                    return $user->getFullName() . ' - ' . $user->getSociete()->getName() . ' <' . $user->getEmail() . '>';
//                },
//                'choice_attr' => function ($user, $key, $index) {
//                    return array(
//                        'data-name' => $user->getFullName(),
//                        'data-address1' => $user->getAddress1(),
//                        'data-address2' => $user->getAddress2(),
//                        'data-zip' => $user->getZip(),
//                        'data-town' => $user->getTown(),
//                        'data-country' => $user->getCountry() ? $user->getCountry()->getId() : 1
//                    );
//                },
//                'data' => $userContact,
//                'required' => false
//            ));
//        });
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
        return 'aw_plansbundle_commande_address';
    }
}