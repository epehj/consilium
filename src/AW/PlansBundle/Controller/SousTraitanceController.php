<?php

namespace AW\PlansBundle\Controller;

use AW\PlansBundle\Entity\Commande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class SousTraitanceController extends Controller
{
  public function index(Request $request)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.seeprod');


    return $this->render('AWPlansBundle:SousTraitance:list.html.twig',
        array('stats' => null));
  }

    public function statsAction(Request $request)
    {
        $this->denyAccessUnlessGranted('webappli.cmdplan.seeprod');


        return $this->render('AWPlansBundle:SousTraitance:list.html.twig',
            array('listStatus' => Commande::$statusName));
    }

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
                'label' => 'Crepaépation',
                'data' => array(
                    $er->countByUserAndStatusBetweenDate($this->getUser(), Commande::STATUS_VALIDATED, $monthStart, $monthEnd),
                    $er->sumByUserAndStatusBetweenDate($this->getUser(), Commande::STATUS_VALIDATED, $monthStart, $monthEnd)
                )
            ),
            array(
                'label' => 'Modification',
                'data' => array(
                    $er->countByUserAndStatusBetweenDate($this->getUser(), Commande::STATUS_BAT_MODIF, $monthStart, $monthEnd),
                    $er->sumByUserAndStatusBetweenDate($this->getUser(), Commande::STATUS_BAT_MODIF, $monthStart, $monthEnd)
                )
            ),
            array(
                'label' => 'Validation',
                'data' => array(
                    $er->countByUserAndStatusBetweenDate($this->getUser(), Commande::STATUS_BAT_VALIDATED, $monthStart, $monthEnd),
                    $er->sumByUserAndStatusBetweenDate($this->getUser(), Commande::STATUS_BAT_VALIDATED, $monthStart, $monthEnd)
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
