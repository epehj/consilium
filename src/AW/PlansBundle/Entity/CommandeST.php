<?php

namespace AW\PlansBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Collections\ArrayCollection;

use AW\DoliBundle\Entity\Societe;

/**
 * Commande
 *
 * @ORM\Table(name="aw_commandeplan_st")
 * @ORM\Entity(repositoryClass="AW\PlansBundle\Repository\CommandeRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="id")
 */
class CommandeST
{
    /**
     * @var int
     *
     * @ORM\Column(name="rowid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Commande
     * @ORM\OneToOne(targetEntity=Commande::class, mappedBy="commandeST")
     * @Assert\Valid
     */
    private $commande;


    /**
     * @var array
     *
     * @ORM\Column(name="infos_complementaires", type="array", nullable=true)
     * @Assert\Type("integer")
     */
    private $infosComplementaires;

    /**
     * @var array
     *
     * @ORM\Column(name="prestation", type="integer")
     * @Assert\Type("integer")
     * @Assert\NotNull()
     * @Assert\GreaterThanOrEqual(1)
     */
    // TODO trouver pourquoi si l'on choisi aucun radiobutton on a pas de messages d'erreur
    /** type de prestation choisie pour le client : Releve (1), Total(2) ou Pose(3) */
    private $prestation;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_batiment", type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $refBatiment;
    /**
     * @var string
     *
     * @ORM\Column(name="ref_commande", type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $refCommande;
    /**
     * @var string
     *
     * @ORM\Column(name="operation", type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $operation;
    /**
     * @var string
     *
     * @ORM\Column(name="type_batiment", type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $typeBatiment;
    /**
     * @var string
     *
     * @ORM\Column(name="url_infos_site", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $urlInfosSite;

    /**
     * @var string
     * @ORM\Column(name="contact1", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $contact1;

    /**
     * @var string
     * @ORM\Column(name="contact2", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $contact2;

    /**
     * @var string
     * @ORM\Column(name="client_final", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $clientFinal;

    /**
     * @var string
     *
     * @ORM\Column(name="acces", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $acces;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="urgence", type="datetime", nullable=true)
     */
    private $urgence;



    const NONE                          = 0;
    const RELEVE_PLANS_DISPO            = 10;
    const VALIDATION_OBLIG              = 20;

    const PRESTA_RELEVE                        = 1;
    const PRESTA_POSE                          = 2;
    const PRESTA_TOTAL                         = 3;

    /**
     * @return int
     */
    public function getInfosComplementaires()
    {
        return $this->infosComplementaires;
    }

    /**
     * @param int $infosComplementaires
     */
    public function setInfosComplementaires($infosComplementaires)
    {
        $this->infosComplementaires = $infosComplementaires;
    }

    /**
     * @return mixed
     */
    public function getPrestation()
    {
        return $this->prestation;
    }

    /**
     * @param mixed $prestation
     */
    public function setPrestation($prestation)
    {
        $this->prestation = $prestation;
    }

    /**
     * @return string
     */
    public function getRefBatiment()
    {
        return $this->refBatiment;
    }

    /**
     * @param string $refBatiment
     */
    public function setRefBatiment($refBatiment)
    {
        $this->refBatiment = $refBatiment;
    }

    /**
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @param string $operation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    /**
     * @return string
     */
    public function getTypeBatiment()
    {
        return $this->typeBatiment;
    }

    /**
     * @param string $typeBatiment
     */
    public function setTypeBatiment($typeBatiment)
    {
        $this->typeBatiment = $typeBatiment;
    }

    /**
     * @return string
     */
    public function getUrlInfosSite()
    {
        return $this->urlInfosSite;
    }

    /**
     * @param string $urlInfosSite
     */
    public function setUrlInfosSite($urlInfosSite)
    {
        $this->urlInfosSite = $urlInfosSite;
    }

    /**
     * @return Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * @param Commande $commande
     */
    public function setCommande($commande)
    {
        $this->commande = $commande;
    }

    /**
     * @return string
     */
    public function getContact1()
    {
        return $this->contact1;
    }

    /**
     * @param string $contact1
     */
    public function setContact1($contact1)
    {
        $this->contact1 = $contact1;
    }

    /**
     * @return string
     */
    public function getContact2()
    {
        return $this->contact2;
    }

    /**
     * @param string $contact2
     */
    public function setContact2($contact2)
    {
        $this->contact2 = $contact2;
    }

    /**
     * @return string
     */
    public function getClientFinal()
    {
        return $this->clientFinal;
    }

    /**
     * @param string $clientFinal
     */
    public function setClientFinal($clientFinal)
    {
        $this->clientFinal = $clientFinal;
    }

    /**
     * @return string
     */
    public function getAcces()
    {
        return $this->acces;
    }

    /**
     * @param string $acces
     */
    public function setAcces($acces)
    {
        $this->acces = $acces;
    }

    /**
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * @param string $commentaire
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
    }

    /**
     * @return string
     */
    public function getUrgence()
    {
        return $this->urgence;
    }

    /**
     * @param string $urgence
     */
    public function setUrgence($urgence)
    {
        $this->urgence = $urgence;
    }

    /**
     * @return string
     */
    public function getRefCommande()
    {
        return $this->refCommande;
    }

    /**
     * @param string $refCommande
     */
    public function setRefCommande($refCommande)
    {
        $this->refCommande = $refCommande;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

//    // TODO probablement déplacer les statuts dans la classe abstraite mais grosse phase de test a prévoir suite aux tests
//    const STATUS_ATTENTE_VALIDATION	    = 0;
//    const STATUS_VALIDATED				= 10;
//    const STATUS_BAT					= 20;
//    const STATUS_BAT_MODIF				= 30;
//    const STATUS_BAT_VALIDATED			= 40;
//    const STATUS_EN_FABRICATION			= 50;
//    const STATUS_RECEIVED				= 60;
//    const STATUS_EN_EXPEDITION			= 70;
//    const STATUS_CLOSED					= 80;
//    const STATUS_CANCELED				= 90;
//
//    public static $statusName = array(
//      self::STATUS_ATTENTE_VALIDATION => 'En attente de validation',
//      self::STATUS_VALIDATED          => 'Validée',
//      self::STATUS_BAT                => 'BAT',
//      self::STATUS_BAT_MODIF          => 'BAT en modification',
//      self::STATUS_BAT_VALIDATED      => 'BAT validé',
//      self::STATUS_EN_FABRICATION     => 'En fabrication',
//      self::STATUS_RECEIVED           => "En attente d'expédition - Dispo en agence",
//      self::STATUS_EN_EXPEDITION      => 'En expédition',
//      self::STATUS_CLOSED             => 'Clôturée',
//      self::STATUS_CANCELED           => 'Annulée'
//    );
//
//    const RELEVE_STATUS_EN_ATTENTE  = 0;
//    const RELEVE_STATUS_TERMINE     = 10;
//    const POSE_STATUS_EN_ATTENTE    = 50;
//    const POSE_STATUS_TERMINE       = 60;
//    const RELEVE_ANOMALIE           = 90;
//
//    public static $releveStatusName = array(
//      self::RELEVE_STATUS_EN_ATTENTE  => 'Relevé en attente tests',
//      self::RELEVE_STATUS_TERMINE     => 'Relevé terminé',
//      self::POSE_STATUS_EN_ATTENTE    => 'Pose en attente',
//      self::POSE_STATUS_TERMINE       => 'Pose terminé',
//      self::RELEVE_ANOMALIE           => 'Anomalie'
//    );

}