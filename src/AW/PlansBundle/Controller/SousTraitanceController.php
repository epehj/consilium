<?php

namespace AW\PlansBundle\Controller;

use AW\PlansBundle\Entity\Commande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;


class SousTraitanceController extends Controller
{
  public function statsAction(Request $request)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.seeprod');

      $groups = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('AWDoliBundle:Usergroup')
          ->findBy(array('id' => $this->getParameter('releveurs_group_id')))
      ;

      $start = (new \DateTime('first day of this month'));

      $end = (new \DateTime('last day of this month'));

      $builder = $this->createFormBuilder(array('start' => $start, 'end' => $end), array('translation_domain' => false));
      $form = $builder
          ->add('start', DateType::class, array(
              'widget' => 'single_text',
              'format' => 'dd-MM-yyyy',
              'html5' => false,
              'attr' => array(
                  'class' => 'bootstrap-datepicker'
              )
          ))
          ->add('end', DateType::class, array(
              'widget' => 'single_text',
              'format' => 'dd-MM-yyyy',
              'html5' => false,
              'attr' => array(
                  'class' => 'bootstrap-datepicker'
              )
          ))
          ->getForm()
      ;

      if($request->isMethod('POST') and $form->handleRequest($request)->isValid()){
          $data = $form->getData();
          $start = $data['start'];
          $end = $data['end'];
      }

      $start->setTime(0, 0, 0);
      $end->setTime(23, 59, 59);


      $er = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('AWPlansBundle:Production')
      ;

      $monthStart = (new \DateTime('first day of this month'))
          ->setTime(0, 0, 0)
      ;

      $monthEnd = (new \DateTime('last day of this month'))
          ->setTime(23, 59, 59)
      ;

      foreach($groups as $group){
          foreach($group->getUsers() as $user){
              $stats[$group->getId()][$user->getId()] = array(
                  'releve' => 0,//$er->getDatas(),
                  'pose' => 0,
                  'inax' => 0,
                  'delai_total'=> 0,
                  'delai_releve'=> 0,
                  'delai_dessin'=> 0,
                  'delai_pose'=> 0
              );
          }
      }


    return $this->render('AWPlansBundle:SousTraitance:soustraitance.html.twig',
        array(
            'stats' => $stats,
            'form'=>$form->createView(),
            'groups'=>$groups));
  }

//    public function statsAction(Request $request)
//    {
//        $this->denyAccessUnlessGranted('webappli.cmdplan.seeprod');
//
//
//        return $this->render('AWPlansBundle:SousTraitance:list.html.twig',
//            array('listStatus' => Commande::$statusName));
//    }

    public function recapRelevesAction(Request $request)
    {
        $this->denyAccessUnlessGranted('webappli.cmdplan.seeprod');
        $er = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AWPlansBundle:Production')
        ;

        $monthStart = (new \DateTime('first day of this month'))
            ->setTime(0, 0, 0)
        ;

        $monthEnd = (new \DateTime('last day of this month'))
            ->setTime(23, 59, 59)
        ;

        $stats = array(
            array(
                'label' => 'Saisie',
                'data' => array(
                    'Commandes',
                    'Plans'
//                    $er->countByUserAndStatusBetweenDate($this->getUser(), Commande::STATUS_VALIDATED, $monthStart, $monthEnd),
//                    $er->sumByUserAndStatusBetweenDate($this->getUser(), Commande::STATUS_VALIDATED, $monthStart, $monthEnd)
                )
            ),
            array(
                'label' => 'Relevés',
                'data' => array(
                    'Commandes',
                    'Plans'
                )
            ),
            array(
                'label' => 'Posés',
                'data' => array(
                    'Commandes',
                    'Plans'
                )
            ),
            array(
                'label' => 'Annulés',
                'data' => array(
                    'Commandes',
                    'Plans'
                )
            ),
            array(
                'label' => 'Délai Moyen Commande saisie->pose terminée',
                'data' => array(
                    'delai moyen en jour'
                )
            ),
            array(
                'label' => 'Délai Moyen Commande saisie->relevé terminé',
                'data' => array(
                    'delai moyen en jour'
                )
            ),
            array(
                'label' => 'Délai Moyen relevé terminé->recept atelier',
                'data' => array(
                    'delai moyen en jour'
                )
            ),
            array(
                'label' => 'Délai Moyen recept atelier->pose term',
                'data' => array(
                    'delai moyen en jour'
                )
            )
        );

        return $this->render('AWPlansBundle:SousTraitance:recapReleves.html.twig', array(
            'stats' => $stats
        ));
    }

    public function releveAction(Request $request)
    {
        $this->denyAccessUnlessGranted('webappli.cmdplan.seeprod');


        return $this->render('AWPlansBundle:SousTraitance:releve.html.twig',
            array('listStatus' => Commande::$statusName));
    }
}
