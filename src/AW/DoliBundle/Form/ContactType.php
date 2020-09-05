<?php

namespace AW\DoliBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ContactType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('lastname', TextType::class, array(
        'label' => 'Nom'
      ))
      ->add('firstname', TextType::class, array(
        'label' => 'Prénom',
        'required' => false
      ))
      ->add('phone', TelType::class, array(
        'label' => 'Téléphone',
        'required' => false
      ))
      ->add('phoneMobile', TelType::class, array(
        'label' => 'Mobile',
        'required' => false
      ))
      ->add('email', EmailType::class, array(
        'label' => 'Email',
        'required' => false
      ))
      ->add('address', TextareaType::class, array(
        'label' => 'Adresse'
      ))
      ->add('zip', TextType::class, array(
        'label' => 'Code postal'
      ))
      ->add('town', TextType::class, array(
        'label' => 'Ville'
      ))
      ->add('country', EntityType::class, array(
        'label' => 'Pays',
        'class' => 'AWDoliBundle:Country',
        'choice_label' => 'name'
      ))
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AW\DoliBundle\Entity\Contact',
      'translation_domain' => false
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'aw_dolibundle_contact';
  }
}
