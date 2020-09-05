<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Commande
 *
 * @ORM\Table(name="llx_commande")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\CommandeRepository")
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
     * @ORM\Column(name="ref", type="string", length=30, unique=true)
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_client", type="string", length=255)
     */
    private $refClient;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_commande", type="date", nullable=true)
     */
    private $dateCommande;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var string
     *
     * @ORM\Column(name="total_ht", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $totalHt;

    /**
     * @var string
     *
     * @ORM\Column(name="total_ttc", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $totalTtc;

    /**
     * @var string
     *
     * @ORM\Column(name="tva", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $tva;

    /**
     * @var int
     *
     * @ORM\Column(name="fk_multicurrency", type="integer", nullable=true)
     */
    private $multicurrency;

    /**
     * @var string
     *
     * @ORM\Column(name="multicurrency_code", type="string", length=255)
     */
    private $multicurrencyCode;

    /**
     * @var string
     *
     * @ORM\Column(name="multicurrency_tx", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $multicurrencyTx;

    /**
     * @var string
     *
     * @ORM\Column(name="multicurrency_total_ht", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $multicurrencyTotalHt;

    /**
     * @var string
     *
     * @ORM\Column(name="multicurrency_total_tva", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $multicurrencyTotalTva;

    /**
     * @var string
     *
     * @ORM\Column(name="multicurrency_total_ttc", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $multicurrencyTotalTtc;

    /**
     * @var int
     *
     * @ORM\Column(name="fk_input_reason", type="integer", nullable=true)
     */
    private $inputReason;

    /**
     * @var int
     *
     * @ORM\Column(name="fk_shipping_method", type="integer", nullable=true)
     */
    private $shippingMethod;

    /**
     * @var int
     *
     * @ORM\Column(name="fk_cond_reglement", type="integer", nullable=true)
     */
    private $condReglement;

    /**
     * @var int
     *
     * @ORM\Column(name="fk_mode_reglement", type="integer", nullable=true)
     */
    private $modeReglement;

    /**
     * @var bool
     *
     * @ORM\Column(name="facture", type="boolean")
     */
    private $billed;

    /**
     * @var int
     *
     * @ORM\Column(name="fk_statut", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var Societe
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Societe")
     * @ORM\JoinColumn(name="fk_soc", referencedColumnName="rowid", nullable=false)
     */
    protected $societe;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_author", referencedColumnName="rowid", nullable=false)
     */
    protected $userCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_valid", type="datetime", nullable=true)
     */
    protected $dateValidation;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_valid", referencedColumnName="rowid", nullable=false)
     */
    protected $userValidation;

    /**
     * @var CommandeDet
     *
     * @ORM\OneToMany(targetEntity="AW\DoliBundle\Entity\CommandeDet", mappedBy="commande", cascade={"remove"})
     */
    protected $listDet;

    /**
     * @var CommandeFacture
     *
     * /!\ Lors d'une requete ajouter obligatoire les conditions "AND sourcetype = 'commande' AND targettype = 'facture'" à la jointure
     *
     * @ORM\OneToMany(targetEntity="AW\DoliBundle\Entity\CommandeFacture", mappedBy="commande")
     */
    protected $coFactures;

    /**
     * @var CommandeExtrafields
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\CommandeExtrafields", mappedBy="commande", cascade={"remove"})
     */
    private $extrafields;

    const SHIPMENT_MODE_CHRONO = 8;
    const SHIPMENT_MODE_AGENCE = 14;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->dateCommande = new \DateTime();
        $this->billed = false;
        $this->multicurrency = 1;
        $this->multicurrencyCode = 'EUR';
        $this->multicurrencyTx = 1;
        $this->inputReason = 13;
        $this->status = 0;
        $this->listDet = new ArrayCollection();
        $this->coFactures = new ArrayCollection();
    }

    /**
     * Get sub link to Dolibarr
     *
     * @return string
     */
    public function getLinkTo()
    {
      return '/commande/card.php?id='.$this->id;
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
     * Set dateCommande
     *
     * @param \DateTime $dateCommande
     *
     * @return Commande
     */
    public function setDateCommande($dateCommande)
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Get dateCommande
     *
     * @return \DateTime
     */
    public function getDateCommande()
    {
        return $this->dateCommande;
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
     * Set totalHt
     *
     * @param string $totalHt
     *
     * @return Commande
     */
    public function setTotalHt($totalHt)
    {
        $this->totalHt = $totalHt;

        return $this;
    }

    /**
     * Get totalHt
     *
     * @return string
     */
    public function getTotalHt()
    {
        return $this->totalHt;
    }

    /**
     * Set totalTtc
     *
     * @param string $totalTtc
     *
     * @return Commande
     */
    public function setTotalTtc($totalTtc)
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }

    /**
     * Get totalTtc
     *
     * @return string
     */
    public function getTotalTtc()
    {
        return $this->totalTtc;
    }

    /**
     * Set tva
     *
     * @param string $tva
     *
     * @return Commande
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return string
     */
    public function getTva()
    {
        return $this->tva;
    }

    /**
     * Set multicurrency
     *
     * @param integer $multicurrency
     *
     * @return Commande
     */
    public function setMulticurrency($multicurrency)
    {
        $this->multicurrency = $multicurrency;

        return $this;
    }

    /**
     * Get multicurrency
     *
     * @return integer
     */
    public function getMulticurrency()
    {
        return $this->multicurrency;
    }

    /**
     * Set multicurrencyCode
     *
     * @param string $multicurrencyCode
     *
     * @return Commande
     */
    public function setMulticurrencyCode($multicurrencyCode)
    {
        $this->multicurrencyCode = $multicurrencyCode;

        return $this;
    }

    /**
     * Get multicurrencyCode
     *
     * @return string
     */
    public function getMulticurrencyCode()
    {
        return $this->multicurrencyCode;
    }

    /**
     * Set multicurrencyTx
     *
     * @param string $multicurrencyTx
     *
     * @return Commande
     */
    public function setMulticurrencyTx($multicurrencyTx)
    {
        $this->multicurrencyTx = $multicurrencyTx;

        return $this;
    }

    /**
     * Get multicurrencyTx
     *
     * @return string
     */
    public function getMulticurrencyTx()
    {
        return $this->multicurrencyTx;
    }

    /**
     * Set multicurrencyTotalHt
     *
     * @param string $multicurrencyTotalHt
     *
     * @return Commande
     */
    public function setMulticurrencyTotalHt($multicurrencyTotalHt)
    {
        $this->multicurrencyTotalHt = $multicurrencyTotalHt;

        return $this;
    }

    /**
     * Get multicurrencyTotalHt
     *
     * @return string
     */
    public function getMulticurrencyTotalHt()
    {
        return $this->multicurrencyTotalHt;
    }

    /**
     * Set multicurrencyTotalTva
     *
     * @param string $multicurrencyTotalTva
     *
     * @return Commande
     */
    public function setMulticurrencyTotalTva($multicurrencyTotalTva)
    {
        $this->multicurrencyTotalTva = $multicurrencyTotalTva;

        return $this;
    }

    /**
     * Get multicurrencyTotalTva
     *
     * @return string
     */
    public function getMulticurrencyTotalTva()
    {
        return $this->multicurrencyTotalTva;
    }

    /**
     * Set multicurrencyTotalTtc
     *
     * @param string $multicurrencyTotalTtc
     *
     * @return Commande
     */
    public function setMulticurrencyTotalTtc($multicurrencyTotalTtc)
    {
        $this->multicurrencyTotalTtc = $multicurrencyTotalTtc;

        return $this;
    }

    /**
     * Get multicurrencyTotalTtc
     *
     * @return string
     */
    public function getMulticurrencyTotalTtc()
    {
        return $this->multicurrencyTotalTtc;
    }

    /**
     * Set inputReason
     *
     * @param integer $inputReason
     *
     * @return Commande
     */
    public function setInputReason($inputReason)
    {
        $this->inputReason = $inputReason;

        return $this;
    }

    /**
     * Get inputReason
     *
     * @return integer
     */
    public function getInputReason()
    {
        return $this->inputReason;
    }

    /**
     * Set shippingMethod
     *
     * @param integer $shippingMethod
     *
     * @return Commande
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    /**
     * Get shippingMethod
     *
     * @return integer
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * Set condReglement
     *
     * @param integer $condReglement
     *
     * @return Commande
     */
    public function setCondReglement($condReglement)
    {
        $this->condReglement = $condReglement;

        return $this;
    }

    /**
     * Get condReglement
     *
     * @return integer
     */
    public function getCondReglement()
    {
        return $this->condReglement;
    }

    /**
     * Set modeReglement
     *
     * @param integer $modeReglement
     *
     * @return Commande
     */
    public function setModeReglement($modeReglement)
    {
        $this->modeReglement = $modeReglement;

        return $this;
    }

    /**
     * Get modeReglement
     *
     * @return integer
     */
    public function getModeReglement()
    {
        return $this->modeReglement;
    }

    /**
     * Set billed
     *
     * @param boolean $billed
     *
     * @return Commande
     */
    public function setBilled($billed)
    {
        $this->billed = $billed;

        return $this;
    }

    /**
     * Get billed
     *
     * @return boolean
     */
    public function getBilled()
    {
        return $this->billed;
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
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set societe
     *
     * @param \AW\DoliBundle\Entity\Societe $societe
     *
     * @return Commande
     */
    public function setSociete(\AW\DoliBundle\Entity\Societe $societe)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return \AW\DoliBundle\Entity\Societe
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
     * Add listDet
     *
     * @param \AW\DoliBundle\Entity\CommandeDet $listDet
     *
     * @return Commande
     */
    public function addListDet(\AW\DoliBundle\Entity\CommandeDet $listDet)
    {
        $this->listDet[] = $listDet;

        return $this;
    }

    /**
     * Remove listDet
     *
     * @param \AW\DoliBundle\Entity\CommandeDet $listDet
     */
    public function removeListDet(\AW\DoliBundle\Entity\CommandeDet $listDet)
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
     * Add coFacture
     *
     * @param \AW\DoliBundle\Entity\CommandeFacture $coFacture
     *
     * @return Commande
     */
    public function addCoFacture(\AW\DoliBundle\Entity\CommandeFacture $coFacture)
    {
        $this->coFactures[] = $coFacture;

        return $this;
    }

    /**
     * Remove coFacture
     *
     * @param \AW\DoliBundle\Entity\CommandeFacture $coFacture
     */
    public function removeCoFacture(\AW\DoliBundle\Entity\CommandeFacture $coFacture)
    {
        $this->coFactures->removeElement($coFacture);
    }

    /**
     * Get coFactures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoFactures()
    {
        return $this->coFactures;
    }

    /**
     * Set extrafields
     *
     * @param \AW\DoliBundle\Entity\CommandeExtrafields $extrafields
     *
     * @return Commande
     */
    public function setExtrafields(\AW\DoliBundle\Entity\CommandeExtrafields $extrafields = null)
    {
        $this->extrafields = $extrafields;

        return $this;
    }

    /**
     * Get extrafields
     *
     * @return \AW\DoliBundle\Entity\CommandeExtrafields
     */
    public function getExtrafields()
    {
        return $this->extrafields;
    }
}
