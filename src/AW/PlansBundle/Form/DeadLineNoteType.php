<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class DeadLineNoteType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('message', TextType::class, array(
        'label' => 'Message'
      ))
      ->add('deadline', DateTimeType::class, array(
        'label' => 'Date de rappel',
        'date_widget' => 'single_text',
        'date_format' => 'dd-MM-yyyy',
        'html5' => false,
        'attr' => array(
          'class' => 'bootstrap-datepicker'
        )
      ))
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AW\PlansBundle\Entity\Note',
      'translation_domain' => false
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'aw_plansbundle_deadline_note';
  }
}
