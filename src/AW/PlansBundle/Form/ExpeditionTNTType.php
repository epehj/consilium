<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ExpeditionTNTType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('serviceCode', ChoiceType::class, array(
        'label' => 'Produit',
        'choices' => array(
          '8:00 Express' => 'N',
          '9:00 Express' => 'A',
          '10:00 Express' => 'T',
          'Express' => 'J',
          'Express (P)' => 'P'
        ),
        'mapped' => false,
        'data' => 'J'
      ))
      ->add('quantity', NumberType::class, array(
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
        'label' => 'Générer les étiquettes TNT',
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
    return 'aw_plansbundle_expedition_tnt';
  }
}
