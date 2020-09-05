<?php

namespace AW\PlansBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use AW\CoreBundle\Form\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use AW\DoliBundle\Repository\ContactRepository;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MailType extends AbstractType
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
        if(!$this->tokenStorage->getToken()->getUser()->getSociete()){
          $commande = $builder->getData()->getCommande();

          $builder
            ->add('addressTo', TextType::class, array(
              'label' => 'Destinataire(s)'
            ))
            ->add('contact', EntityType::class, array(
              'label' => 'Contact',
              'class' => 'AWDoliBundle:Contact',
              'query_builder' => function(ContactRepository $er) use($commande){
                return $er->getSocieteContactsQueryBuilder($commande->getSociete());
              },
              'choice_label' => function($contact){
                return $contact->getFullName().' - '.$contact->getPhoneOrMobile().' <'.$contact->getEmail().'>';
              },
              'choice_attr' => function($contact, $key, $index){
                return array(
                  'data-email' => $contact->getFullName().' <'.$contact->getEmail().'>'
                );
              },
              'required' => false,
              'mapped' => false
            ))
            ->add('subject', TextType::class, array(
              'label' => 'Sujet'
            ))
          ;
        }

        $builder
          ->add('message', CKEditorType::class, array(
            'label' => 'Message'
          ))
          ->add('attachments', FileType::class, array(
            'label' => 'Pièce(s) jointe(s)',
            'mapped' => false,
            'multiple' => true,
            'required' => $options['required_attachments']
          ))
        ;

        if($options['attachments_selectable']){
          $builder
            ->add('attachments2', CollectionType::class, array(
              'label' => 'Fichiers disponibles',
              'mapped' => false,
              'entry_type' => CheckboxType::class
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
            'data_class' => 'AW\PlansBundle\Entity\Mail',
            'translation_domain' => false,
            'required_attachments' => true,
            'attachments_selectable' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'aw_plansbundle_mail';
    }
}
