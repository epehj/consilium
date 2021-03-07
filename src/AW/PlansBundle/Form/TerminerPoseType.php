<?php

namespace AW\PlansBundle\Form;

use AW\DoliBundle\Repository\UserRepository;
use AW\PlansBundle\Entity\Anomalie;
use AW\PlansBundle\Repository\AnomalieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TerminerPoseType extends AbstractType
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
        $builder
            //le field remarques vient de la commande
            ->add('remarques', TextareaType::class, array(
                'label' => 'Remarques',
                'required' => false
            ))
            ->add('anomalies', EntityType::class, array(
                'class' => 'AWPlansBundle:Anomalie',
                'choice_label' => 'label',
                'query_builder' => function(AnomalieRepository $em) {
                    return $em->getAnomaliesPose();
                },
                'multiple' => true,
                'expanded' => true,
            ))
            ->add('remarqueAno', TextType::class, array(
                'label' => 'Remarque',
                'required' => false
            ))
            ->add('typeBatiment', EntityType::class, array(
                'label' => 'Type de bat.',
                'class' => 'AWPlansBundle:TypeBatiment',
                'choice_label' => 'type'
            ))
            ->add('commande', AggregationCommandePoseType::class)
            ->add('acces', TextType::class, array(
                'required' => false
            ))
            ->add('inaccessible', ChoiceType::class, array(
                'label'=> 'Le lieu était il inaccessible ?',
                'required' => false,
                'choices' => array(
                    'Oui' => true,
                    'Non' => false
                )
            ))
            ->add('remarqueInaccessible', TextType::class, array(
                'label' => 'Remarque',
                'required' => false
            ))
//        ->add('commande', ContactBatNameType::class)

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
        return 'aw_plansbundle_terminer_pose';
    }
}