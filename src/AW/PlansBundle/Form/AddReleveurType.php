<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use AW\DoliBundle\Repository\UserRepository;

class AddReleveurType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('releveur', EntityType::class, array(
        'label' => 'Releveur/Poseur',
        'class' => 'AWDoliBundle:User',
        'query_builder' => function(UserRepository $er) {
          return $er->getUsersInReleveurGroupQueryBuilder();
        },
        'choice_label' => function($user){
          return $user->getFullName().' <'.$user->getEmail().'>';
        }
      ))
    ;
  }

//  /**
//   * {@inheritdoc}
//   */
//  public function configureOptions(OptionsResolver $resolver)
//  {
//    $resolver->setDefaults(array(
//      'data_class' => 'AW\PlansBundle\Entity\Commande',
//      'translation_domain' => false
//    ));
//  }

//  /**
//   * {@inheritdoc}
//   */
//  public function getBlockPrefix()
//  {
//    return 'aw_plansbundle_commande_releve_note';
//  }
}
