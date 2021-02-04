<?php

namespace AW\PlansBundle\Form;

use AW\PlansBundle\Entity\CommandeST;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AW\PlansBundle\Form\CommandeDetType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use AW\DoliBundle\Entity\Societe;
use AW\DoliBundle\Repository\SocieteRepository;
use AW\DoliBundle\Repository\UserRepository;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Constraints\Choice;

class CommandeSousTraitantType extends AbstractType
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
    $user = $this->tokenStorage->getToken()->getUser();
    $builder
        ->add('prestation', ChoiceType::class, array(
            'label' =>'Type de prestation',
            'required' => true,
            'expanded' => true,
            'multiple' => false,
            'choices' => array(
                "STR - Relevé" => CommandeST::PRESTA_RELEVE,
                "STT - Total" => CommandeST::PRESTA_TOTAL,
                "STP - Pose" => CommandeST::PRESTA_POSE
            )
        ))
        ->add('operation', TextType::class, array(
            'label' => 'Réf. Opération',
            'required' => false
        ))
        ->add('refCommande', TextType::class, array(
            'label' => 'Réf. commande client/Tournée',
            'required' => false
        ))
        ->add('refBatiment', TextType::class, array(
            'label' => 'Réf. Bâtiment',
            'required' => false
        ))
        ->add('typeBatiment', ChoiceType::class, array(
            'label' => 'Type Bâtiment',
            'choices' => array(
                'ERP' => 'ERP',
                'Habitation' => 'Habitation',
                'École' => 'Ecole',
                'Parking' => 'Parking',
                'Hôtel' => 'Hotel',
                'Hôpital' =>'Hopital',
                'Chambre' => 'Chambre',
                'Bateau'=> 'Bateau'
            ),
            'required' => false
        ))
        ->add('urlInfosSite', TextType::class, array(
            'label' => 'URL Infos site',
            'required' => false
        ))
        ->add('contact1', TextType::class, array(
            'label' => 'Contact 1',
            'required' => false
        ))
        ->add('contact2', TextType::class, array(
            'label' => 'Contact 2',
            'required' => false
        ))
        ->add('clientFinal', TextType::class, array(
            'label' => 'Client Final',
            'required' => false
        ))
        ->add('acces', TextType::class, array(
            'label' => 'Accès',
            'required' => false
        ))
        ->add('commentaire', TextAreaType::class, array(
            'label' => 'Commentaires',
            'required' => false
        ))
        ->add('urgence',DateType::class, array(
            'widget' => 'single_text',
            'label' => 'Date limite',
            'format' => 'dd-MM-yyyy',
            'html5' => false,
            'required' => false,
            'attr' => array(
                'class' => 'bootstrap-datepicker'
            )
        ))
        ->add('commande', CommandeType::class, array(
            'label' => 'label child commande a supprimer',
            'required' => false
        ))
//        ->add('infosComplementaires', ChoiceType::class, array(
//            'label' => 'Informations complémentaires',
//            'required' => false,
//            'expanded' => true,
//            'multiple' => true,
//            'choices' => array(
//                'Relevé/Plan disponible' => 10,
//                'Validation obligatoire par le client' => 20
//            )
//        ))
    ;

  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AW\PlansBundle\Entity\CommandeST',
      'translation_domain' => false
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'aw_plansbundle_commande_st';
  }
}
