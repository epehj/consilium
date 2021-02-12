<?php

namespace AW\PlansBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Collections\ArrayCollection;

class AnomalieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', EntityType::class, array(
                'label' => 'Type d\'anomalie',
                'class' => 'AWPlansBundle:Anomalie',
                'choice_label' => 'label',
//                'query_builder' => function(AnomalieRepository $er){
//                    return $er->getAnomalieFromId(1);
//                },
//                'choice_label' => function($anomalie) {
//                    return $anomalie->getLabel();
//                }
//            ,
                'multiple' => true,
                'expanded' => true,
//                "empty_data" => new ArrayCollection
            ));
//        ->add('label', TextType::class);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AW\PlansBundle\Entity\Anomalie'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'aw_plansbundle_anomalie';
    }


}
