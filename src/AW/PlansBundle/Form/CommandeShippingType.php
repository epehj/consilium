<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CommandeShippingType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $commande = $builder->getData();

    $builder
      ->add('shippingRecipient', TextType::class, array(
        'label' => 'Nom',
        'data' => $commande->getShippingRecipient() ? $commande->getShippingRecipient() : $commande->getSociete()->getName()
      ))
      ->add('shippingAddress1', TextType::class, array(
        'label' => 'Adresse',
        'data' => $commande->getShippingAddress1() ? $commande->getShippingAddress1() : $commande->getSociete()->getAddress1()
      ))
      ->add('shippingAddress2', TextType::class, array(
        'label' => 'Adresse complémentaire',
        'data' => $commande->getShippingAddress2() ? $commande->getShippingAddress2() : $commande->getSociete()->getAddress2(),
        'required' => false
      ))
      ->add('shippingZip', TextType::class, array(
        'label' => 'Code postal',
        'data' => $commande->getShippingZip() ? $commande->getShippingZip() : $commande->getSociete()->getZip()
      ))
      ->add('shippingTown', TextType::class, array(
        'label' => 'Ville',
        'data' => $commande->getShippingTown() ? $commande->getShippingTown() : $commande->getSociete()->getTown()
      ))
      ->add('shippingCountry', EntityType::class, array(
        'label' => 'Pays',
        'class' => 'AWDoliBundle:Country',
        'choice_label' => 'name',
        'data' => $commande->getShippingCountry() ? $commande->getShippingCountry() : $commande->getSociete()->getCountry()
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
    return 'aw_plansbundle_commande_shipping';
  }
}
