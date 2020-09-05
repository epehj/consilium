<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CommandeSiteType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $commande = $builder->getData();

    $builder
      ->add('site', TextType::class, array(
        'label' => 'Nom du site'
      ))
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
      ->add('releve', ChoiceType::class, array(
        'label' => 'Relevés plans',
        'choices' => array(
          'Oui' => true,
          'Non' => false
        )
      ))
      ->add('pose', ChoiceType::class, array(
        'label' => 'Pose plans',
        'choices' => array(
          'Oui' => true,
          'Non' => false
        )
      ))
      ->add('releveNote', TextType::class, array(
        'label' => 'Note de relevé/pose',
        'required' => false,
        'attr' => array(
          'maxlength' => 50
        )
      ))
      ->add('remarques', TextareaType::class, array(
        'label' => 'Remarques',
        'required' => false
      ))
    ;

    if(($commande->getSociete()->getInfosPlans() and $commande->getSociete()->getInfosPlans()->getAllowContactBat()) or $commande->getContactBATName()){
      $builder
        ->add('contactBATName', TextType::class, array(
          'label' => 'Nom du contact',
          'required' => false
        ))
        ->add('contactBATPhone', TextType::class, array(
          'label' => 'Téléphone',
          'required' => false
        ))
        ->add('contactBATEmail', TextType::class, array(
          'label' => 'E-Mail',
          'required' => false
        ))
      ;
    }
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
    return 'aw_plansbundle_commande_site';
  }
}
