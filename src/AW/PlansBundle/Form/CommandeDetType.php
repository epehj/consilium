<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use AW\PlansBundle\Entity\CommandeDet;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommandeDetType extends AbstractType
{
  private $tokenStorage;

  public function __construct(TokenStorageInterface $tokenStorage)
  {
    $this->tokenStorage = $tokenStorage;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $finitions = array();
    foreach(CommandeDet::$finitionName as $key => $name){
      if(!is_int($key) and $key == 'CCC'){
        continue;
      }

      $finitions[$name] = $key;
    }

    $builder
      ->add('type', HiddenType::class)
      ->add('qty', IntegerType::class, array(
        'label' => 'Quantité'
      ))
      ->add('format', ChoiceType::class, array(
        'label' => 'Format',
        'choices' => array_flip(CommandeDet::$formatName)
      ))
      ->add('matiere', ChoiceType::class, array(
        'label' => 'Matière CommandeDetType.php',
        'choices' => array_flip(CommandeDet::$matiereName)
      ))
      ->add('hole', ChoiceType::class, array(
        'label' => 'Avec trou ?',
        'choices' => array(
          'Oui' => true,
          'Non' => false
        )
      ))
      ->add('background', ChoiceType::class, array(
        'label' => 'Fond',
        'choices' => array_flip(CommandeDet::$backgroundName),
        'required' => false
      ))
      ->add('design', ChoiceType::class, array(
        'label' => 'Design',
        'choices' => array_flip(CommandeDet::$designName),
        'required' => false
      ))
      ->add('vue', ChoiceType::class, array(
        'label' => 'Vue',
        'choices' => array_flip(CommandeDet::$vueName)
      ))
      ->add('finition', ChoiceType::class, array(
        'label' => 'Finition',
        'choices' => $finitions
      ))
    ;

    if(!$this->tokenStorage->getToken()->getUser()->getSociete()){
      $builder->add('remise', NumberType::class, array(
        'label' => 'Remise',
        'scale' => 2
      ));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AW\PlansBundle\Entity\CommandeDet',
      'translation_domain' => false
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'aw_plansbundle_commandedet';
  }
}
