<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use AW\DoliBundle\Repository\UserRepository;

class CommandeReleveNoteType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('releveNote', TextType::class, array(
        'label' => 'Note',
        'attr' => array(
          'maxlength' => 50
        )
      ))
      ->add('releveUser', EntityType::class, array(
        'label' => 'Releveur/Poseur',
        'class' => 'AWDoliBundle:User',
        'query_builder' => function(UserRepository $er) {
          return $er->getJMOUnderResponsibilityQueryBuilder();
        },
        'choice_label' => function($user){
          return $user->getFullName().' <'.$user->getEmail().'>';
        }
      ))
      ->add('date', DateTimeType::class, array(
        'label' => 'Date',
        'date_widget' => 'single_text',
        'date_format' => 'dd-MM-yyyy',
        'html5' => false,
        'attr' => array(
          'class' => 'bootstrap-datepicker'
        ),
        'mapped' => false
      ))
    ;
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
    return 'aw_plansbundle_commande_releve_note';
  }
}
