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
          ->getRepository('AWPlansBundle:Commande')
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
                  'releve' => array(
                      'commande' => $er->countByReleveBetweenDateAndByUser($user,  $start, $end),
                      'plan' => $er->countPlansByReleveBetweenDateAndByUser($user,  $start, $end)),
                  'pose' => array(
                      'commande' => $er->countByPoseBetweenDateAndByUser($user, $start, $end),
                      'plan' => $er->countPlansByPoseBetweenDateAndByUser($user, $start, $end),),
                  'inax' => array (
                      'commande' => $er->countByStatusBetweenDateByUser($user, Commande::STATUS_CANCELED, $start, $end),
                      'plan' => $er->countPlansByStatusBetweenDateByUser($user, Commande::STATUS_CANCELED, $start, $end)),
                  'delai_total'=> $er->averagePoseTimeBetweenDateByUser($user, $start, $end),
                  'delai_releve'=> $er->averageReleveTimeBetweenDateByUser($user, $start, $end),
                  'delai_dessin'=> $er->averageReleveToReceiveTimeBetweenDateByUser($user, $start, $end),
                  'delai_pose'=> $er->averageReceiveToPoseTimeBetweenDateByUser($user, $start, $end)
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
            ->getRepository('AWPlansBundle:Commande')
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
                    $er->countCommandeSaisie($monthStart, $monthEnd),
                    $er->countPlanSaisi($monthStart, $monthEnd),
                )
            ),
            array(
                'label' => 'Relevés',
                'data' => array(
                    $er->countByReleveBetweenDate( $monthStart, $monthEnd),
                    $er->countPlansReleveBetweenDate($monthStart, $monthEnd)
                )
            ),
            array(
                'label' => 'Posés',
                'data' => array(
                    $er->countByPoseBetweenDate($monthStart, $monthEnd), //$er->countBetweenDateWithStatus(Commande::, $monthStart, $monthend),
                    $er->countPlansPoseBetweenDate($monthStart, $monthEnd)
                )
            ),
            array(
                'label' => 'Annulés',
                'data' => array(
                    $er->countByStatusBetweenDate(Commande::STATUS_CANCELED, $monthStart, $monthEnd),
                    $er->countPlansByStatusBetweenDate(Commande::STATUS_CANCELED, $monthStart, $monthEnd)
                )
            ),
            array(
                'label' => 'Délai Moyen Commande saisie->pose terminée',
                'data' => array(
                    $er->averagePoseTimeBetweenDate($monthStart, $monthEnd),
                )
            ),
            array(
                'label' => 'Délai Moyen Commande saisie->relevé terminé',
                'data' => array(
                    $er->averageReleveTimeBetweenDate($monthStart, $monthEnd)
                )
            ),
            array(
                'label' => 'Délai Moyen relevé terminé->recept atelier',
                'data' => array(
                    $er->averageReleveToReceiveTimeBetweenDate($monthStart, $monthEnd)
                )
            ),
            array(
                'label' => 'Délai Moyen recept atelier->pose term',
                'data' => array(
                    $er->averageReceiveToPoseTimeBetweenDate($monthStart, $monthEnd)
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
