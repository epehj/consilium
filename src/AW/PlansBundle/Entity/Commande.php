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
 * @ORM\Table(name="aw_commandeplan")
 * @ORM\Entity(repositoryClass="AW\PlansBundle\Repository\CommandeRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="ref")
 */
class Commande
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
     * @var string
     *
     * @ORM\Column(name="ref", type="string", length=25, unique=true)
     * @Assert\Length(min=4,max=25)
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_client", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $refClient;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="address1", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $address1;

    /**
     * @var string
     *
     * @ORM\Column(name="address2", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=25)
     * @Assert\NotBlank()
     * @Assert\Length(max=25)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $town;

    /**
     * @var array
     *
     * @ORM\Column(name="geo_code", type="array", nullable=true)
     */
    private $geoCode;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_recipient", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $shippingRecipient;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_address1", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $shippingAddress1;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_address2", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $shippingAddress2;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_zip", type="string", length=25, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=25)
     */
    private $shippingZip;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_town", type="string", length=50, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $shippingTown;

    /**
     * @var bool
     *
     * @ORM\Column(name="releve", type="boolean")
     * @Assert\Type("bool")
     */
    private $releve;

    /**
     * @var bool
     *
     * @ORM\Column(name="releve_status", type="integer", nullable=true)
     * @Assert\Type("integer")
     */
    private $releveStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="releve_date", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $releveDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_releve_user", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $releveUser;

    /**
     * @var bool
     *
     * @ORM\Column(name="releve_note", type="string", length=255, nullable=true)
     * @Assert\Length(max=50)
     */
    private $releveNote;

    /**
     * @var bool
     *
     * @ORM\Column(name="pose", type="boolean")
     * @Assert\Type("bool")
     */
    private $pose;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pose_date", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $poseDate;

    /**
     * @var int
     *
     * @ORM\Column(name="qty_declination", type="integer")
     * @Assert\Type("integer")
     * @Assert\GreaterThanOrEqual(0)
     */
    private $qtyDeclination;

    /**
     * @var string
     *
     * @ORM\Column(name="remarques", type="text", nullable=true)
     */
    private $remarques;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     * @Assert\DateTime()
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_validation", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateValidation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateModification;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fabrication", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateFabrication;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_receive", type="datetime", nullable=true)
     */
    private $dateReceive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_expedition", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateExpedition;

    /**
     * @var Expedition
     *
     * @ORM\ManyToOne(targetEntity="AW\PlansBundle\Entity\Expedition", inversedBy="commandes")
     * @ORM\JoinColumn(name="fk_expedition", referencedColumnName="rowid")
     */
    private $expedition;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_close", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateClose;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_cancel", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateCancel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_update", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateUpdate;

    /**
     * @var bool
     *
     * @ORM\Column(name="erp_imported", type="boolean")
     * @Assert\Type("bool")
     */
    private $erpImported;

    /**
     * @var bool
     *
     * @ORM\Column(name="urgent", type="boolean")
     * @Assert\Type("bool")
     */
    private $urgent;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     * @Assert\Type("integer")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_production", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateProduction;

    /**
     * @var int
     *
     * @ORM\Column(name="production", type="integer", nullable=true)
     */
    private $production;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_relance", type="integer", nullable=true)
     * @Assert\Type("integer")
     */
    private $nbRelance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_relance", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateRelance;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $userAgent;

    /**
     * @var string
     *
     * @ORM\Column(name="dir", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $dir;

    /**
     * @var bool
     *
     * @ORM\Column(name="bat_only_by_fr", type="boolean")
     * @Assert\Type("bool")
     */
    private $batOnlyByFr;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_bat_name", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $contactBATName;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_bat_phone", type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    private $contactBATPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_bat_email", type="string", length=255, nullable=true)
     * @Assert\Email()
     * @Assert\Length(max=255)
     */
    private $contactBATEmail;

    /**
     * @var int
     *
     * @ORM\Column(name="alert", type="integer", nullable=true)
     * @Assert\Type("integer")
     */
    private $alert;

    /**
     * @var Societe
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Societe")
     * @ORM\JoinColumn(name="fk_societe", referencedColumnName="rowid", nullable=false)
     * @Assert\Valid()
     */
    protected $societe;

    /**
     * @var Doli Commande
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\Commande", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="fk_doli", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $doliCommande;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_creation", referencedColumnName="rowid", nullable=false)
     * @Assert\Valid()
     */
    protected $userCreation;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Country")
     * @ORM\JoinColumn(name="shipping_fk_country", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $shippingCountry;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_contact", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $userContact;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_validation", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $userValidation;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_modification", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $userModification;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_fabrication", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $userFabrication;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_receive", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $userReceive;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_expedition", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $userExpedition;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_close", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $userClose;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_cancel", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $userCancel;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_production", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $userProduction;

    /**
     * @var CommandeDet
     *
     * @ORM\OneToMany(targetEntity="AW\PlansBundle\Entity\CommandeDet", mappedBy="commande", cascade={"remove"})
     * @Assert\Valid()
     * @Assert\Count(min=1, minMessage="Une commande doit contenir au moins un plan.")
     */
    private $listDet;

    /**
     * @var Note
     *
     * @ORM\OneToMany(targetEntity="AW\PlansBundle\Entity\Note", mappedBy="commande", cascade={"remove"})
     * @Assert\Valid()
     */
    private $notes;

    /**
     * @var Mail
     *
     * @ORM\OneToMany(targetEntity="AW\PlansBundle\Entity\Mail", mappedBy="commande", cascade={"remove"})
     * @Assert\Valid()
     */
    private $mails;

    /**
     * @var Bat
     *
     * @ORM\OneToMany(targetEntity="AW\PlansBundle\Entity\Bat", mappedBy="commande", cascade={"remove"})
     * @Assert\Valid()
     */
    private $bats;

    /**
     * @var Production
     *
     * @ORM\OneToMany(targetEntity="AW\PlansBundle\Entity\Production", mappedBy="commande", cascade={"remove"})
     * @Assert\Valid()
     */
    private $productions;

    const STATUS_ATTENTE_VALIDATION	= 0;
    const STATUS_VALIDATED					= 10;
    const STATUS_BAT								= 20;
    const STATUS_BAT_MODIF					= 30;
    const STATUS_BAT_VALIDATED			= 40;
    const STATUS_EN_FABRICATION			= 50;
    const STATUS_RECEIVED						= 60;
    const STATUS_EN_EXPEDITION			= 70;
    const STATUS_CLOSED							= 80;
    const STATUS_CANCELED						= 90;

    public static $statusName = array(
      self::STATUS_ATTENTE_VALIDATION => 'En attente de validation',
      self::STATUS_VALIDATED          => 'Validée',
      self::STATUS_BAT                => 'BAT',
      self::STATUS_BAT_MODIF          => 'BAT en modification',
      self::STATUS_BAT_VALIDATED      => 'BAT validé',
      self::STATUS_EN_FABRICATION     => 'En fabrication',
      self::STATUS_RECEIVED           => "En attente d'expédition - Dispo en agence",
      self::STATUS_EN_EXPEDITION      => 'En expédition',
      self::STATUS_CLOSED             => 'Clôturée',
      self::STATUS_CANCELED           => 'Annulée'
    );

    const RELEVE_STATUS_EN_ATTENTE  = 0;
    const RELEVE_STATUS_TERMINE     = 10;
    const POSE_STATUS_EN_ATTENTE    = 50;
    const POSE_STATUS_TERMINE       = 60;
    const RELEVE_ANOMALIE           = 90;

    public static $releveStatusName = array(
      self::RELEVE_STATUS_EN_ATTENTE  => 'Relevé en attente',
      self::RELEVE_STATUS_TERMINE     => 'Relevé terminé',
      self::POSE_STATUS_EN_ATTENTE    => 'Pose en attente',
      self::POSE_STATUS_TERMINE       => 'Pose terminé',
      self::RELEVE_ANOMALIE           => 'Anomalie'
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->status = self::STATUS_ATTENTE_VALIDATION;
        $this->dateCreation = new \DateTime();
        $this->dateUpdate = new \DateTime();
        $this->production = 0;
        $this->dir = '';
        $this->batOnlyByFr = false;
        $this->erpImported = false;
        $this->urgent = false;
        $this->alert = 0;
        $this->releve = false;
        $this->pose = false;
        $this->qtyDeclination = 0;
        $this->nbRelance = 0;
        $this->listDet = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->mails = new ArrayCollection();
        $this->bats = new ArrayCollection();
        $this->productions = new ArrayCollection();
    }

    public function __clone()
    {
        $this->status = self::STATUS_ATTENTE_VALIDATION;
        $this->dateCreation = new \DateTime();
        $this->dateUpdate = new \DateTime();
        $this->production = 0;
        $this->dir = '';
        $this->batOnlyByFr = false;
        $this->erpImported = false;
        $this->urgent = false;
        $this->alert = 0;
        $this->releve = false;
        $this->releveStatus = null;
        $this->pose = false;
        $this->qtyDeclination = 0;
        $this->nbRelance = 0;
        $this->expedition = null;
        $this->listDet = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->mails = new ArrayCollection();
        $this->bats = new ArrayCollection();
        $this->productions = new ArrayCollection();

        $this->dateValidation = null;
        $this->dateModification = null;
        $this->dateFabrication = null;
        $this->dateReceive = null;
        $this->dateExpedition = null;
        $this->dateClose = null;
        $this->dateCancel = null;
        $this->dateUpdate = null;
        $this->dateProduction = null;
        $this->dateRelance = null;
        $this->doliCommande = null;
        $this->userCreation = null;
        $this->userValidation = null;
        $this->userModification = null;
        $this->userFabrication = null;
        $this->userReceive = null;
        $this->userExpedition = null;
        $this->userClose = null;
        $this->userCancel = null;
        $this->userProduction = null;
    }

    public function getStatusLabel()
    {
      switch($this->status){
        case self::STATUS_ATTENTE_VALIDATION:
          return 'En attente de validation';
          break;
        case self::STATUS_VALIDATED:
          return 'Validée';
          break;
        case self::STATUS_BAT:
          return 'BAT '.count($this->bats);
          break;
        case self::STATUS_BAT_MODIF:
          return 'BAT '.count($this->bats).' en modification';
          break;
        case self::STATUS_BAT_VALIDATED:
          return 'BAT validé';
          break;
        case self::STATUS_EN_FABRICATION:
          return 'En fabrication';
          break;
        case self::STATUS_RECEIVED:
          return "En attente d'expédition - Dispo en agence";
          break;
        case self::STATUS_EN_EXPEDITION:
          return 'En expédition';
          break;
        case self::STATUS_CLOSED:
          return 'Clôturée';
          break;
        case self::STATUS_CANCELED:
          return 'Annulée';
          break;
        default:
          return '';
          break;
      }
    }

    public function getReleveStatusLabel()
    {
      if(!$this->releve and !$this->pose){
        return '';
      }

      return isset(self::$releveStatusName[$this->releveStatus]) ? self::$releveStatusName[$this->releveStatus] : '';
    }

    /**
     * @Assert\Callback
     */
    public function isValid(ExecutionContextInterface $context)
    {
      // relevés obligatoires sauf si demande de relevé sur site
      if(!$this->releve and $this->id === null){
        $dir = sys_get_temp_dir().'/'.$this->dir;
        $finder = new Finder();
        $finder->files()->notName('logo.*')->in($dir);
        if(iterator_count($finder) == 0){
          $context
            ->buildViolation('Merci de joindre au moins un relevé.')
            ->addViolation()
          ;
        }
      }

      if(
        (empty($this->contactBATName) and ($this->contactBATPhone or $this->contactBATEmail)) or
        (empty($this->contactBATPhone) and ($this->contactBATName or $this->contactBATEmail)) or
        (empty($this->contactBATEmail) and ($this->contactBATPhone or $this->contactBATName))
      ){
        $context
          ->buildViolation('Merci de remplir tous les champs si vous souhaitez ajouter une tierce personne pour les BATs. Sinon, ne rien mettre.')
          ->atPath('contactBATName')
          ->addViolation()
        ;
      }
    }

    public function isCanBeValidate()
    {
      if($this->status < self::STATUS_VALIDATED){
        return true;
      }else{
        return false;
      }
    }

    public function isCanBeSentBAT()
    {
      if(
        ($this->status == self::STATUS_VALIDATED or $this->status == self::STATUS_BAT_MODIF) and
        ($this->production == 0 or $this->production == 3) and
        (!$this->releve or ($this->releve and $this->releveStatus == self::RELEVE_STATUS_TERMINE))
      ){
        return true;
      }else{
        return false;
      }
    }

    public function isCanBePrinted()
    {
      if($this->status == self::STATUS_BAT_VALIDATED and ($this->production == 0 or $this->production == 3)){
        return true;
      }else{
        return false;
      }
    }

    public function isCanBeModify()
    {
      if($this->status < self::STATUS_EN_EXPEDITION){
        return true;
      }else{
        return false;
      }
    }

    public function isWaiting()
    {
      return ($this->status == self::STATUS_ATTENTE_VALIDATION ? true : false);
    }

    public function isCanceled()
    {
      return ($this->status == self::STATUS_CANCELED ? true : false);
    }

    public function updateStatus(\AW\DoliBundle\Entity\User $user, $status)
    {
      $now = new \DateTime();

      switch($status){
        case self::STATUS_ATTENTE_VALIDATION:
          $this->production = 0;
          $this->urgent = false;
          $this->alert = 1;
          $this->releveStatus = null;
          break;

        case self::STATUS_VALIDATED:
          if($this->societe->getCustomerBad() == Societe::CUSTOMER_BAD_BLACK){
            throw new \Exception('Impossible de valider : client bloqué');
          }

          $this->userValidation = $user;
          $this->dateValidation = $now;

          if($this->releve){
            $this->production = 0;
            $this->releveStatus = self::RELEVE_STATUS_EN_ATTENTE;
          }else{
            $this->production = 1;
            $this->dateProduction = $now;
          }

          if($this->societe->getInfosPlans() and $this->societe->getInfosPlans()->getAutoUrgent()){
            $this->urgent = true;
          }else{
            $this->urgent = false;
          }

          $this->alert = 1;

          if($this->doliCommande){
            $this->doliCommande
              ->setStatus(1)
              ->setDateValidation($now)
              ->setUserValidation($user)
            ;
          }

          if($this->status == self::STATUS_CANCELED){
            $this->userCancel = null;
            $this->dateCancel = null;
          }
          break;

        case self::STATUS_BAT:
          $this->production = 0;
          $this->urgent = false;
          $this->alert = 0;
          break;

        case self::STATUS_BAT_MODIF:
        case self::STATUS_BAT_VALIDATED:
          $this->production = 1;
          $this->dateProduction = $now;
          if($this->societe->getInfosPlans() and $this->societe->getInfosPlans()->getAutoUrgent()){
            $this->urgent = true;
          }else{
            $this->urgent = false;
          }
          $this->alert = 1;
          break;

        case self::STATUS_EN_FABRICATION:
          if($this->societe->getCustomerBad() == Societe::CUSTOMER_BAD_RED){
            throw new \Exception('Impossible de mettre en fabrication: client bloqué');
          }

          $this->userFabrication = $user;
          $this->dateFabrication = $now;
          $this->production = 0;
          $this->urgent = false;
          $this->alert = 0;
          break;

        case self::STATUS_RECEIVED:
          $this->userReceive = $user;
          $this->dateReceive = $now;
          $this->production = 0;
          $this->urgent = 0;
          $this->alert = 0;
          if($this->pose){
            $this->releveStatus = self::POSE_STATUS_EN_ATTENTE;
          }
          break;

        case self::STATUS_EN_EXPEDITION:
          $this->userExpedition = $user;
          $this->dateExpedition = $now;
          $this->production = 0;
          $this->urgent = false;
          $this->alert = 0;

          if($this->doliCommande){
            $this->doliCommande->setStatus(3);
          }
          break;

        case self::STATUS_CANCELED:
          $this->userCancel = $user;
          $this->dateCancel = $now;
          $this->production = 0;
          $this->urgent = false;
          $this->alert = 0;
          $this->releveStatus = null;

          if($this->doliCommande){
            $this->doliCommande->setStatus(-1);
          }
          break;

        case self::STATUS_CLOSED:
          $this->userClose = $user;
          $this->dateClose = $now;
          $this->production = 0;
          $this->urgent = false;
          $this->alert = 0;

          if($this->doliCommande){
            $this->doliCommande->setStatus(3);
          }
          break;

        default:
          throw new \Exception('Changement en statut '.$status.' impossible');
      }

      $this->status = $status;
    }

    public function getDelayMoyenBAT(\AW\CoreBundle\Service\Utils $utils)
    {
      if(count($this->bats) == 0){
        return 0;
      }

      $delay = 0;
      $dateStart = $this->dateValidation ? $this->dateValidation : $this->dateCreation;
      foreach($this->bats as $bat){
        $delay += (($bat->getDateCreation()->getTimestamp() - $dateStart->getTimestamp()) / 86400);

        $dateNext = clone $dateStart;
        $dateNext
          ->setTime(0, 0, 0)
          ->modify('+1 day')
        ;

        while($dateNext < $bat->getDateCreation()){
          if($utils->isDayOff($dateNext)){
            $delay--;
          }

          $dateNext->modify('+1 day');
        }

        $dateStart = $bat->getDateCreation();
      }

      return $delay / count($this->bats);
    }

    public function getDelayBATValid(\AW\CoreBundle\Service\Utils $utils)
    {
      if($this->dateFabrication == null){
        return 0;
      }

      if(count($this->bats) == 0){
        return 0;
      }

      $lastBAT = $this->bats[count($this->bats) - 1];
      if($lastBAT->getDateValidation() == null){
        return 0;
      }

      $delay = (($this->dateFabrication->getTimestamp() - $lastBAT->getDateValidation()->getTimestamp())/86400);

      $dateNext = clone $lastBAT->getDateValidation();
      while($dateNext < $this->dateFabrication){
        if($utils->isDayOff($dateNext)){
          $delay--;
        }

        $dateNext->modify('+1 day');
      }

      return $delay;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ref
     *
     * @param string $ref
     *
     * @return Commande
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set refClient
     *
     * @param string $refClient
     *
     * @return Commande
     */
    public function setRefClient($refClient)
    {
        $this->refClient = $refClient;

        return $this;
    }

    /**
     * Get refClient
     *
     * @return string
     */
    public function getRefClient()
    {
        return $this->refClient;
    }

    /**
     * Set site
     *
     * @param string $site
     *
     * @return Commande
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set address1
     *
     * @param string $address1
     *
     * @return Commande
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     *
     * @return Commande
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Commande
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set town
     *
     * @param string $town
     *
     * @return Commande
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set geoCode
     *
     * @param array $geoCode
     *
     * @return Commande
     */
    public function setGeoCode($geoCode)
    {
        $this->geoCode = $geoCode;

        return $this;
    }

    /**
     * Get geoCode
     *
     * @return array
     */
    public function getGeoCode()
    {
        return $this->geoCode;
    }

    /**
     * Set shippingRecipient
     *
     * @param string $shippingRecipient
     *
     * @return Commande
     */
    public function setShippingRecipient($shippingRecipient)
    {
        $this->shippingRecipient = $shippingRecipient;

        return $this;
    }

    /**
     * Get shippingRecipient
     *
     * @return string
     */
    public function getShippingRecipient()
    {
        return $this->shippingRecipient;
    }

    /**
     * Set shippingAddress1
     *
     * @param string $shippingAddress1
     *
     * @return Commande
     */
    public function setShippingAddress1($shippingAddress1)
    {
        $this->shippingAddress1 = $shippingAddress1;

        return $this;
    }

    /**
     * Get shippingAddress1
     *
     * @return string
     */
    public function getShippingAddress1()
    {
        return $this->shippingAddress1;
    }

    /**
     * Set shippingAddress2
     *
     * @param string $shippingAddress2
     *
     * @return Commande
     */
    public function setShippingAddress2($shippingAddress2)
    {
        $this->shippingAddress2 = $shippingAddress2;

        return $this;
    }

    /**
     * Get shippingAddress2
     *
     * @return string
     */
    public function getShippingAddress2()
    {
        return $this->shippingAddress2;
    }

    /**
     * Set shippingZip
     *
     * @param string $shippingZip
     *
     * @return Commande
     */
    public function setShippingZip($shippingZip)
    {
        $this->shippingZip = $shippingZip;

        return $this;
    }

    /**
     * Get shippingZip
     *
     * @return string
     */
    public function getShippingZip()
    {
        return $this->shippingZip;
    }

    /**
     * Set shippingTown
     *
     * @param string $shippingTown
     *
     * @return Commande
     */
    public function setShippingTown($shippingTown)
    {
        $this->shippingTown = $shippingTown;

        return $this;
    }

    /**
     * Get shippingTown
     *
     * @return string
     */
    public function getShippingTown()
    {
        return $this->shippingTown;
    }

    /**
     * Set releve
     *
     * @param boolean $releve
     *
     * @return Commande
     */
    public function setReleve($releve)
    {
        $this->releve = $releve;

        return $this;
    }

    /**
     * Get releve
     *
     * @return bool
     */
    public function getReleve()
    {
        return $this->releve;
    }

    /**
     * Set remarques
     *
     * @param string $remarques
     *
     * @return Commande
     */
    public function setRemarques($remarques)
    {
        $this->remarques = $remarques;

        return $this;
    }

    /**
     * Get remarques
     *
     * @return string
     */
    public function getRemarques()
    {
        return $this->remarques;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Commande
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateValidation
     *
     * @param \DateTime $dateValidation
     *
     * @return Commande
     */
    public function setDateValidation($dateValidation)
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    /**
     * Get dateValidation
     *
     * @return \DateTime
     */
    public function getDateValidation()
    {
        return $this->dateValidation;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return Commande
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set dateFabrication
     *
     * @param \DateTime $dateFabrication
     *
     * @return Commande
     */
    public function setDateFabrication($dateFabrication)
    {
        $this->dateFabrication = $dateFabrication;

        return $this;
    }

    /**
     * Get dateFabrication
     *
     * @return \DateTime
     */
    public function getDateFabrication()
    {
        return $this->dateFabrication;
    }

    /**
     * Set dateReceive
     *
     * @param \DateTime $dateReceive
     *
     * @return Commande
     */
    public function setDateReceive($dateReceive)
    {
        $this->dateReceive = $dateReceive;

        return $this;
    }

    /**
     * Get dateReceive
     *
     * @return \DateTime
     */
    public function getDateReceive()
    {
        return $this->dateReceive;
    }

    /**
     * Set dateExpedition
     *
     * @param \DateTime $dateExpedition
     *
     * @return Commande
     */
    public function setDateExpedition($dateExpedition)
    {
        $this->dateExpedition = $dateExpedition;

        return $this;
    }

    /**
     * Get dateExpedition
     *
     * @return \DateTime
     */
    public function getDateExpedition()
    {
        return $this->dateExpedition;
    }

    /**
     * Set expedition
     *
     * @param \AW\PlansBundle\Entity\Expedition $expedition
     *
     * @return Commande
     */
    public function setExpedition(\AW\PlansBundle\Entity\Expedition $expedition = null)
    {
        $this->expedition = $expedition;

        return $this;
    }

    /**
     * Get expedition
     *
     * @return \AW\PlansBundle\Entity\Expedition
     */
    public function getExpedition()
    {
        return $this->expedition;
    }

    /**
     * Set dateClose
     *
     * @param \DateTime $dateClose
     *
     * @return Commande
     */
    public function setDateClose($dateClose)
    {
        $this->dateClose = $dateClose;

        return $this;
    }

    /**
     * Get dateClose
     *
     * @return \DateTime
     */
    public function getDateClose()
    {
        return $this->dateClose;
    }

    /**
     * Set dateCancel
     *
     * @param \DateTime $dateCancel
     *
     * @return Commande
     */
    public function setDateCancel($dateCancel)
    {
        $this->dateCancel = $dateCancel;

        return $this;
    }

    /**
     * Get dateCancel
     *
     * @return \DateTime
     */
    public function getDateCancel()
    {
        return $this->dateCancel;
    }

    /**
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     *
     * @return Commande
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set erpImported
     *
     * @param boolean $erpImported
     *
     * @return Commande
     */
    public function setErpImported($erpImported)
    {
        $this->erpImported = $erpImported;

        return $this;
    }

    /**
     * Get erpImported
     *
     * @return bool
     */
    public function getErpImported()
    {
        return $this->erpImported;
    }

    /**
     * Set urgent
     *
     * @param boolean $urgent
     *
     * @return Commande
     */
    public function setUrgent($urgent)
    {
        $this->urgent = $urgent;

        return $this;
    }

    /**
     * Get urgent
     *
     * @return bool
     */
    public function getUrgent()
    {
        return $this->urgent;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Commande
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateProduction
     *
     * @param \DateTime $dateProduction
     *
     * @return Commande
     */
    public function setDateProduction($dateProduction)
    {
        $this->dateProduction = $dateProduction;

        return $this;
    }

    /**
     * Get dateProduction
     *
     * @return \DateTime
     */
    public function getDateProduction()
    {
        return $this->dateProduction;
    }

    /**
     * Set production
     *
     * @param integer $production
     *
     * @return Commande
     */
    public function setProduction($production)
    {
        $this->production = $production;

        return $this;
    }

    /**
     * Get production
     *
     * @return integer
     */
    public function getProduction()
    {
        return $this->production;
    }

    /**
     * Set nbRelance
     *
     * @param integer $nbRelance
     *
     * @return Commande
     */
    public function setNbRelance($nbRelance)
    {
        $this->nbRelance = $nbRelance;

        return $this;
    }

    /**
     * Get nbRelance
     *
     * @return int
     */
    public function getNbRelance()
    {
        return $this->nbRelance;
    }

    /**
     * Set dateRelance
     *
     * @param \DateTime $dateRelance
     *
     * @return Commande
     */
    public function setDateRelance($dateRelance)
    {
        $this->dateRelance = $dateRelance;

        return $this;
    }

    /**
     * Get dateRelance
     *
     * @return \DateTime
     */
    public function getDateRelance()
    {
        return $this->dateRelance;
    }

    /**
     * Set userAgent
     *
     * @param string $userAgent
     *
     * @return Commande
     */
    public function setUserAgent($userAgent = null)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get userAgent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set dir
     *
     * @param string $dir
     *
     * @return Commande
     */
    public function setDir($dir)
    {
        $this->dir = $dir;

        return $this;
    }

    /**
     * Get dir
     *
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Set batOnlyByFr
     *
     * @param boolean $batOnlyByFr
     *
     * @return Commande
     */
    public function setBatOnlyByFr($batOnlyByFr)
    {
        $this->batOnlyByFr = $batOnlyByFr;

        return $this;
    }

    /**
     * Get batOnlyByFr
     *
     * @return boolean
     */
    public function getBatOnlyByFr()
    {
        return $this->batOnlyByFr;
    }

    /**
     * Set contactBATName
     *
     * @param string $contactBATName
     *
     * @return Commande
     */
    public function setContactBATName($contactBATName)
    {
        $this->contactBATName = $contactBATName;

        return $this;
    }

    /**
     * Get contactBATName
     *
     * @return string
     */
    public function getContactBATName()
    {
        return $this->contactBATName;
    }

    /**
     * Set contactBATPhone
     *
     * @param string $contactBATPhone
     *
     * @return Commande
     */
    public function setContactBATPhone($contactBATPhone)
    {
        $this->contactBATPhone = $contactBATPhone;

        return $this;
    }

    /**
     * Get contactBATPhone
     *
     * @return string
     */
    public function getContactBATPhone()
    {
        return $this->contactBATPhone;
    }

    /**
     * Set contactBATEmail
     *
     * @param string $contactBATEmail
     *
     * @return Commande
     */
    public function setContactBATEmail($contactBATEmail)
    {
        $this->contactBATEmail = $contactBATEmail;

        return $this;
    }

    /**
     * Get contactBATEmail
     *
     * @return string
     */
    public function getContactBATEmail()
    {
        return $this->contactBATEmail;
    }

    /**
     * Set alert
     *
     * @param integer $alert
     *
     * @return Commande
     */
    public function setAlert($alert)
    {
        $this->alert = $alert;

        return $this;
    }

    /**
     * Get alert
     *
     * @return integer
     */
    public function getAlert()
    {
        return $this->alert;
    }

    /**
     * Set societe
     *
     * @param Societe $societe
     *
     * @return Commande
     */
    public function setSociete(Societe $societe)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return Societe
     */
    public function getSociete()
    {
        return $this->societe;
    }

    /**
     * Set userCreation
     *
     * @param \AW\DoliBundle\Entity\User $userCreation
     *
     * @return Commande
     */
    public function setUserCreation(\AW\DoliBundle\Entity\User $userCreation)
    {
        $this->userCreation = $userCreation;

        return $this;
    }

    /**
     * Get userCreation
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserCreation()
    {
        return $this->userCreation;
    }

    /**
     * Set userValidation
     *
     * @param \AW\DoliBundle\Entity\User $userValidation
     *
     * @return Commande
     */
    public function setUserValidation(\AW\DoliBundle\Entity\User $userValidation)
    {
        $this->userValidation = $userValidation;

        return $this;
    }

    /**
     * Get userValidation
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserValidation()
    {
        return $this->userValidation;
    }

    /**
     * Set userContact
     *
     * @param \AW\DoliBundle\Entity\User $userContact
     *
     * @return Commande
     */
    public function setUserContact(\AW\DoliBundle\Entity\User $userContact = null)
    {
        $this->userContact = $userContact;

        return $this;
    }

    /**
     * Get userContact
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserContact()
    {
        return $this->userContact;
    }

    /**
     * Set userModification
     *
     * @param \AW\DoliBundle\Entity\User $userModification
     *
     * @return Commande
     */
    public function setUserModification(\AW\DoliBundle\Entity\User $userModification = null)
    {
        $this->userModification = $userModification;

        return $this;
    }

    /**
     * Get userModification
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserModification()
    {
        return $this->userModification;
    }

    /**
     * Set userFabrication
     *
     * @param \AW\DoliBundle\Entity\User $userFabrication
     *
     * @return Commande
     */
    public function setUserFabrication(\AW\DoliBundle\Entity\User $userFabrication = null)
    {
        $this->userFabrication = $userFabrication;

        return $this;
    }

    /**
     * Get userFabrication
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserFabrication()
    {
        return $this->userFabrication;
    }

    /**
     * Set userReceive
     *
     * @param \AW\DoliBundle\Entity\User $userReceive
     *
     * @return Commande
     */
    public function setUserReceive(\AW\DoliBundle\Entity\User $userReceive = null)
    {
        $this->userReceive = $userReceive;

        return $this;
    }

    /**
     * Get userReceive
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserReceive()
    {
        return $this->userReceive;
    }

    /**
     * Set userExpedition
     *
     * @param \AW\DoliBundle\Entity\User $userExpedition
     *
     * @return Commande
     */
    public function setUserExpedition(\AW\DoliBundle\Entity\User $userExpedition = null)
    {
        $this->userExpedition = $userExpedition;

        return $this;
    }

    /**
     * Get userExpedition
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserExpedition()
    {
        return $this->userExpedition;
    }

    /**
     * Set userClose
     *
     * @param \AW\DoliBundle\Entity\User $userClose
     *
     * @return Commande
     */
    public function setUserClose(\AW\DoliBundle\Entity\User $userClose = null)
    {
        $this->userClose = $userClose;

        return $this;
    }

    /**
     * Get userClose
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserClose()
    {
        return $this->userClose;
    }

    /**
     * Set userCancel
     *
     * @param \AW\DoliBundle\Entity\User $userCancel
     *
     * @return Commande
     */
    public function setUserCancel(\AW\DoliBundle\Entity\User $userCancel = null)
    {
        $this->userCancel = $userCancel;

        return $this;
    }

    /**
     * Get userCancel
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserCancel()
    {
        return $this->userCancel;
    }

    /**
     * Set userProduction
     *
     * @param \AW\DoliBundle\Entity\User $userProduction
     *
     * @return Commande
     */
    public function setUserProduction(\AW\DoliBundle\Entity\User $userProduction = null)
    {
        $this->userProduction = $userProduction;

        return $this;
    }

    /**
     * Get userProduction
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserProduction()
    {
        return $this->userProduction;
    }

    /**
     * Set doliCommande
     *
     * @param \AW\DoliBundle\Entity\Commande $doliCommande
     *
     * @return Commande
     */
    public function setDoliCommande(\AW\DoliBundle\Entity\Commande $doliCommande = null)
    {
        $this->doliCommande = $doliCommande;

        return $this;
    }

    /**
     * Get doliCommande
     *
     * @return \AW\DoliBundle\Entity\Commande
     */
    public function getDoliCommande()
    {
        return $this->doliCommande;
    }

    /**
     * Set shippingCountry
     *
     * @param \AW\DoliBundle\Entity\Country $shippingCountry
     *
     * @return Commande
     */
    public function setShippingCountry(\AW\DoliBundle\Entity\Country $shippingCountry = null)
    {
        $this->shippingCountry = $shippingCountry;

        return $this;
    }

    /**
     * Get shippingCountry
     *
     * @return \AW\DoliBundle\Entity\Country
     */
    public function getShippingCountry()
    {
        return $this->shippingCountry;
    }

    /**
     * Set releveStatus
     *
     * @param boolean $releveStatus
     *
     * @return Commande
     */
    public function setReleveStatus($releveStatus)
    {
        $this->releveStatus = $releveStatus;

        return $this;
    }

    /**
     * Get releveStatus
     *
     * @return boolean
     */
    public function getReleveStatus()
    {
        return $this->releveStatus;
    }

    /**
     * Set releveDate
     *
     * @param \DateTime $releveDate
     *
     * @return Commande
     */
    public function setReleveDate($releveDate)
    {
        $this->releveDate = $releveDate;

        return $this;
    }

    /**
     * Get releveDate
     *
     * @return \DateTime
     */
    public function getReleveDate()
    {
        return $this->releveDate;
    }

    /**
     * Set releveUser
     *
     * @param \AW\DoliBundle\Entity\User $releveUser
     *
     * @return Commande
     */
    public function setReleveUser(\AW\DoliBundle\Entity\User $releveUser = null)
    {
        $this->releveUser = $releveUser;

        return $this;
    }

    /**
     * Get releveUser
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getReleveUser()
    {
        return $this->releveUser;
    }

    /**
     * Set releveNote
     *
     * @param string $releveNote
     *
     * @return Commande
     */
    public function setReleveNote($releveNote)
    {
        $this->releveNote = $releveNote;

        return $this;
    }

    /**
     * Get releveNote
     *
     * @return string
     */
    public function getReleveNote()
    {
        return $this->releveNote;
    }

    /**
     * Set pose
     *
     * @param boolean $pose
     *
     * @return Commande
     */
    public function setPose($pose)
    {
        $this->pose = $pose;

        return $this;
    }

    /**
     * Get pose
     *
     * @return boolean
     */
    public function getPose()
    {
        return $this->pose;
    }

    /**
     * Set poseDate
     *
     * @param \DateTime $poseDate
     *
     * @return Commande
     */
    public function setPoseDate($poseDate)
    {
        $this->poseDate = $poseDate;

        return $this;
    }

    /**
     * Get poseDate
     *
     * @return \DateTime
     */
    public function getPoseDate()
    {
        return $this->poseDate;
    }

    /**
     * Set qtyDeclination
     *
     * @param integer $qtyDeclination
     *
     * @return Commande
     */
    public function setQtyDeclination($qtyDeclination)
    {
        $this->qtyDeclination = $qtyDeclination;

        return $this;
    }

    /**
     * Get qtyDeclination
     *
     * @return integer
     */
    public function getQtyDeclination()
    {
        return $this->qtyDeclination;
    }

    /**
     * Add listDet
     *
     * @param \AW\PlansBundle\Entity\CommandeDet $listDet
     *
     * @return Commande
     */
    public function addListDet(\AW\PlansBundle\Entity\CommandeDet $listDet)
    {
        $this->listDet[] = $listDet;

        return $this;
    }

    /**
     * Remove listDet
     *
     * @param \AW\PlansBundle\Entity\CommandeDet $listDet
     */
    public function removeListDet(\AW\PlansBundle\Entity\CommandeDet $listDet)
    {
        $this->listDet->removeElement($listDet);
    }

    /**
     * Get listDet
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListDet()
    {
        return $this->listDet;
    }

    /**
     * Add note
     *
     * @param \AW\PlansBundle\Entity\Note $note
     *
     * @return Commande
     */
    public function addNote(\AW\PlansBundle\Entity\Note $note)
    {
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param \AW\PlansBundle\Entity\Note $note
     */
    public function removeNote(\AW\PlansBundle\Entity\Note $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Add mail
     *
     * @param \AW\PlansBundle\Entity\Mail $mail
     *
     * @return Commande
     */
    public function addMail(\AW\PlansBundle\Entity\Mail $mail)
    {
        $this->mails[] = $mail;

        return $this;
    }

    /**
     * Remove mail
     *
     * @param \AW\PlansBundle\Entity\Mail $mail
     */
    public function removeMail(\AW\PlansBundle\Entity\Mail $mail)
    {
        $this->mails->removeElement($mail);
    }

    /**
     * Get mails
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMails()
    {
        return $this->mails;
    }

    /**
     * Add bat
     *
     * @param \AW\PlansBundle\Entity\Bat $bat
     *
     * @return Commande
     */
    public function addBat(\AW\PlansBundle\Entity\Bat $bat)
    {
        $this->bats[] = $bat;

        return $this;
    }

    /**
     * Remove bat
     *
     * @param \AW\PlansBundle\Entity\Bat $bat
     */
    public function removeBat(\AW\PlansBundle\Entity\Bat $bat)
    {
        $this->bats->removeElement($bat);
    }

    /**
     * Get bats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBats()
    {
        return $this->bats;
    }

    /**
     * Add production
     *
     * @param \AW\PlansBundle\Entity\Production $production
     *
     * @return Commande
     */
    public function addProduction(\AW\PlansBundle\Entity\Production $production)
    {
        $this->productions[] = $production;

        return $this;
    }

    /**
     * Remove production
     *
     * @param \AW\PlansBundle\Entity\Production $production
     */
    public function removeProduction(\AW\PlansBundle\Entity\Production $production)
    {
        $this->productions->removeElement($production);
    }

    /**
     * Get productions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductions()
    {
        return $this->productions;
    }
}
