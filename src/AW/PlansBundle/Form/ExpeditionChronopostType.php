<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ExpeditionChronopostType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('productCode', ChoiceType::class, array(
        'label' => 'Produit',
        'choices' => array(
          'Chrono 10' => '02',
          'Chrono 13' => '01',
          'Chrono 13 (Instance Agence)' => '1S',
          'Chrono Classic' => '44',
          'Chrono Express' => '17',
          'Chrono 13 (Instance Relais)' => '1T'
        ),
        'mapped' => false,
        'data' => '1S'
      ))
      ->add('numberOfParcel', NumberType::class, array(
        'label' => 'Nombre de colis',
        'mapped' => false,
        'data' => '1'
      ))
      ->add('weight', NumberType::class, array(
        'label' => 'Poids unitaires (en Kg)',
        'mapped' => false,
        'data' => '1'
      ))
      ->add('send', SubmitType::class, array(
        'label' => 'Générer les étiquettes Chronopost',
        'attr' => array('class' => 'btn-primary')
      ))
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AW\PlansBundle\Entity\Expedition',
      'translation_domain' => false
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'aw_plansbundle_expedition_chronopost';
  }
}
