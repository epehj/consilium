<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

use AW\PlansBundle\Entity\Commande;
use AW\DoliBundle\Repository\UserRepository;

class CommandeEditType extends AbstractType
{
  private $tokenStorage;
  private $authorizationChecker;

  public function __construct(TokenStorageInterface $tokenStorage, AuthorizationChecker $authorizationChecker)
  {
    $this->tokenStorage = $tokenStorage;
    $this->authorizationChecker = $authorizationChecker;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $commande = $builder->getData();

    $builder
      ->add('userContact', EntityType::class, array(
        'label' => 'Contact client',
        'class' => 'AWDoliBundle:User',
        'query_builder' => function(UserRepository $er) use($commande){
          return $er->getSocieteUsersQueryBuilder($commande->getSociete());
        },
        'choice_label' => function($user){
          return $user->getFullName().' - '.$user->getSociete()->getName().' <'.$user->getEmail().'>';
        },
        'required' => false
      ))
      ->add('refClient', TextType::class, array(
        'label' => 'Ref. Client',
        'required' => false
      ))
    ;

    if(!$this->tokenStorage->getToken()->getUser()->getSociete() and $this->authorizationChecker->isGranted('webappli.cmdplan.chgstatut')){
      $builder
        ->add('urgent', ChoiceType::class, array(
          'label' => 'Urgent',
          'required' => true,
          'choices' => array(
            'Oui' => true,
            'Non' => false
          )
        ))
        ->add('batOnlyByFr', ChoiceType::class, array(
          'label' => 'BAT client',
          'required' => true,
          'choices' => array(
            'Uniquement par un opérateur français' => true,
            'Par tout opérateur ayant le droit' => false
          )
        ))
        ->add('qtyDeclination', IntegerType::class, array(
          'label' => 'Nombre de déclinaisons'
        ))
        ->add('status', ChoiceType::class, array(
          'label' => 'Statut',
          'required' => true,
          'choices' => array_flip(Commande::$statusName)
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
    return 'aw_plansbundle_commande_edit';
  }
}
