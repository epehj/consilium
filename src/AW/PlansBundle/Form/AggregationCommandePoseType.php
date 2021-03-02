<?php


namespace AW\PlansBundle\Form;


use AW\DoliBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** Form qui regroupe toutes les données propres a une commande, que l'on afficher dans la sous commande */
class AggregationCommandePoseType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            // FIXME vérfier que le contact voulu est bien le contactBatName d'une commande
            ->add('contactBatName', TextType::class, array(
                'label' => 'Contact',
                'required' => false
            ))
            // si l'on utilise le AddReleveurType, le user affecté est enregistré dans une nouvelle commande
            ->add('poseur', EntityType::class, array(
                'label' => 'Affecter un poseur',
                'class' => 'AWDoliBundle:User',
                'query_builder' => function(UserRepository $er) {
                    return $er->getUsersInReleveurGroupQueryBuilder();
                },
                'choice_label' => function($user){
                    return $user->getFullName().' <'.$user->getEmail().'>';
                }
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
        return 'aw_plansbundle_commande_address';
    }
}