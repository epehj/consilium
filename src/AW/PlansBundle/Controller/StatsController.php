<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use AW\PlansBundle\Entity\Commande;

class StatsController extends Controller
{
  public function recapAction()
  {
    $this->denyAccessUnlessGranted('webappli.showcaplan');

    $cache = new FilesystemCache();

    $er = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWPlansBundle:Commande')
    ;

    $todayStart = (new \DateTime())
      ->setTime(0, 0, 0)
    ;

    $todayEnd = (new \DateTime())
      ->setTime(23, 59, 59)
    ;

    $monthStart = (new \DateTime('first day of this month'))
      ->setTime(0, 0, 0)
    ;

    $monthEnd = (new \DateTime('last day of this month'))
      ->setTime(23, 59, 59)
    ;

    $stats = array(
      array(
        'label' => 'Commandes du jour',
        'data' => array(
          $er->countBetweenDate($todayStart, $todayEnd),
          $er->sumQtyBetweenDate($todayStart, $todayEnd)
        )
      ),
      array(
        'label' => 'Commandes du mois',
        'data' => array(
          $er->countBetweenDate($monthStart, $monthEnd),
          $er->sumQtyBetweenDate($monthStart, $monthEnd)
        )
      ),
      array(
        'label' => 'Créa en cours',
        'data' => array(
          $er->countByStatus(Commande::STATUS_VALIDATED),
          $er->sumQtyByStatus(Commande::STATUS_VALIDATED)
        )
      ),
      array(
        'label' => 'BAT Client en cours',
        'data' => array(
          $er->countByStatus(Commande::STATUS_BAT),
          $er->sumQtyByStatus(Commande::STATUS_BAT)
        )
      ),
      array(
        'label' => 'BAT en modification en cours',
        'data' => array(
          $er->countByStatus(Commande::STATUS_BAT_MODIF),
          $er->sumQtyByStatus(Commande::STATUS_BAT_MODIF)
        )
      ),
      array(
        'label' => 'BAT Validé en cours',
        'data' => array(
          $er->countByStatus(Commande::STATUS_BAT_VALIDATED),
          $er->sumQtyByStatus(Commande::STATUS_BAT_VALIDATED)
        )
      ),
      array(
        'label' => 'Commandes en fabrication ce mois-ci',
        'data' => array(
          $er->countEnFabricationBetweenDate($monthStart, $monthEnd),
          $er->sumQtyEnFabricationBetweenDate($monthStart, $monthEnd)
        )
      ),
      array(
        'label' => 'Delai moyen BAT en jours (mois et année) *',
        'data' => array(
          $cache->has('stats.delay_bat.month') ? $cache->get('stats.delay_bat.month') : 'nc',
          $cache->has('stats.delay_bat.year') ? $cache->get('stats.delay_bat.year') : 'nc'
        )
      ),
      array(
        'label' => 'Nombre de BAT moyen (mois et année) *',
        'data' => array(
          $cache->has('stats.number_bat.month') ? $cache->get('stats.number_bat.month') : 'nc',
          $cache->has('stats.number_bat.year') ? $cache->get('stats.number_bat.year') : 'nc'
        )
      ),
      array(
        'label' => 'Delai moyen BAT validé en jours (mois et année) *',
        'data' => array(
          $cache->has('stats.delay_bat_valid.month') ? $cache->get('stats.delay_bat_valid.month') : 'nc',
          $cache->has('stats.delay_bat_valid.year') ? $cache->get('stats.delay_bat_valid.year') : 'nc'
        )
      ),
    );

    if($cache->has('stats.lastupdate')){
      $lastupdate = new \DateTime();
      $lastupdate->setTimestamp($cache->get('stats.lastupdate'));
    }

    return $this->render('AWPlansBundle:Stats:recap.html.twig', array(
      'stats' => $stats,
      'lastupdate' => isset($lastupdate) ? $lastupdate : null
    ));
  }

  public function recapProductionAction()
  {
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
        'label' => 'Création',
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

    return $this->render('AWPlansBundle:Stats:recapProduction.html.twig', array(
      'stats' => $stats
    ));
  }

  public function productionAction(Request $request)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.seeprod');

    $er = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWPlansBundle:Production')
    ;

    $groups = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWDoliBundle:Usergroup')
      ->findBy(array('id' => $this->getParameter('operateurs_ts_groups_id')))
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

    $stats = array();

    foreach($groups as $group){
      foreach($group->getUsers() as $user){
        $stats[$group->getId()][$user->getId()] = array(
          'creation' => array(
            $er->countByUserAndStatusBetweenDate($user, Commande::STATUS_VALIDATED, $start, $end),
            $er->sumByUserAndStatusBetweenDate($user, Commande::STATUS_VALIDATED, $start, $end),
            $er->sumByTimeSpend($user, Commande::STATUS_VALIDATED, $start, $end),
            $er->getSumRenvoiByUser($user, Commande::STATUS_VALIDATED, $start, $end)
          ),
          'modification' => array(
            $er->countByUserAndStatusBetweenDate($user, Commande::STATUS_BAT_MODIF, $start, $end),
            $er->sumByUserAndStatusBetweenDate($user, Commande::STATUS_BAT_MODIF, $start, $end)
          ),
          'validation' => array(
            $er->countByUserAndStatusBetweenDate($user, Commande::STATUS_BAT_VALIDATED, $start, $end),
            $er->sumByUserAndStatusBetweenDate($user, Commande::STATUS_BAT_VALIDATED, $start, $end)
          )
        );
      }
    }

    return $this->render('AWPlansBundle:Stats:production.html.twig', array(
      'form' => $form->createView(),
      'groups' => $groups,
      'stats' => $stats
    ));
  }
}
