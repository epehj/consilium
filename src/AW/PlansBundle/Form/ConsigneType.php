<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AW\CoreBundle\Form\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ConsigneType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('content', CKEditorType::class, array(
        'label' => 'Consigne'
      ))
      ->add('save', SubmitType::class, array(
        'label' => 'Enregistrer'
      ))
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AW\PlansBundle\Entity\Consigne',
      'translation_domain' => false
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'aw_plansbundle_consigne';
  }
}
