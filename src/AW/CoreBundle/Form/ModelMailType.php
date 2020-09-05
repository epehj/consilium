<?php

namespace AW\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use AW\CoreBundle\Form\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModelMailType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('subject', TextType::class, array(
        'label' => 'Objet'
      ))
      ->add('content', CKEditorType::class, array(
        'label' => 'Message'
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
        'data_class' => 'AW\CoreBundle\Entity\ModelMail',
        'translation_domain' => false
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'aw_corebundle_modelmail';
  }
}
