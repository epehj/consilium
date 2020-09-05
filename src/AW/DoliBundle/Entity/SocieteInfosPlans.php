<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SocieteInfosPlans
 *
 * @ORM\Table(name="llx_cust_llx_cust_infos_plans_extrafields_extrafields")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\SocieteInfosPlansRepository")
 */
class SocieteInfosPlans
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
     * @var bool
     *
     * @ORM\Column(name="no_auto_relance", type="boolean", nullable=true)
     */
    private $noAutoRelance;

    /**
     * @var string
     *
     * @ORM\Column(name="note_privee", type="text", nullable=true)
     */
    private $notePrivate;

    /**
     * @var bool
     *
     * @ORM\Column(name="auto_urgent", type="boolean", nullable=true)
     */
    private $autoUrgent;

    /**
     * @var bool
     *
     * @ORM\Column(name="bat_only_by_fr", type="boolean", nullable=true)
     */
    private $batOnlyByFr;

    /**
     * @var bool
     *
     * @ORM\Column(name="allow_contact_bat", type="boolean", nullable=true)
     */
    private $allowContactBat;

    /**
     * @var string
     *
     * @ORM\Column(name="email_bl_pose", type="string", length=128, nullable=true)
     */
    private $emailBLPose;

    /**
     * @var string
     *
     * @ORM\Column(name="signature_bat", type="text", nullable=true)
     */
    private $signatureBat;

    /**
     * @var bool
     *
     * @ORM\Column(name="no_shipping_costs", type="boolean", nullable=true)
     */
    private $noShippingCosts;

    /**
     * @var bool
     *
     * @ORM\Column(name="shipping_costs_by_qty_plans", type="boolean", nullable=true)
     */
    private $shippingCostsByQtyPlans;

    /**
     * @var Societe
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\Societe", inversedBy="infosPlans")
     * @ORM\JoinColumn(name="fk_object", referencedColumnName="rowid")
     */
    private $societe;

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
     * Set noAutoRelance
     *
     * @param boolean $noAutoRelance
     *
     * @return SocieteInfosPlans
     */
    public function setNoAutoRelance($noAutoRelance)
    {
        $this->noAutoRelance = $noAutoRelance;

        return $this;
    }

    /**
     * Get noAutoRelance
     *
     * @return bool
     */
    public function getNoAutoRelance()
    {
        return $this->noAutoRelance;
    }

    /**
     * Set notePrivate
     *
     * @param string $notePrivate
     *
     * @return SocieteInfosPlans
     */
    public function setNotePrivate($notePrivate)
    {
        $this->notePrivate = $notePrivate;

        return $this;
    }

    /**
     * Get notePrivate
     *
     * @return string
     */
    public function getNotePrivate()
    {
        return $this->notePrivate;
    }

    /**
     * Set autoUrgent
     *
     * @param boolean $autoUrgent
     *
     * @return SocieteInfosPlans
     */
    public function setAutoUrgent($autoUrgent)
    {
        $this->autoUrgent = $autoUrgent;

        return $this;
    }

    /**
     * Get autoUrgent
     *
     * @return bool
     */
    public function getAutoUrgent()
    {
        return $this->autoUrgent;
    }

    /**
     * Set batOnlyByFr
     *
     * @param boolean $batOnlyByFr
     *
     * @return SocieteInfosPlans
     */
    public function setBatOnlyByFr($batOnlyByFr)
    {
        $this->batOnlyByFr = $batOnlyByFr;

        return $this;
    }

    /**
     * Get batOnlyByFr
     *
     * @return bool
     */
    public function getBatOnlyByFr()
    {
        return $this->batOnlyByFr;
    }

    /**
     * Set allowContactBat
     *
     * @param boolean $allowContactBat
     *
     * @return SocieteInfosPlans
     */
    public function setAllowContactBat($allowContactBat)
    {
        $this->allowContactBat = $allowContactBat;

        return $this;
    }

    /**
     * Get allowContactBat
     *
     * @return bool
     */
    public function getAllowContactBat()
    {
        return $this->allowContactBat;
    }

    /**
     * Set emailBLPose
     *
     * @param string $emailBLPose
     *
     * @return SocieteInfosPlans
     */
    public function setEmailBLPose($emailBLPose)
    {
        $this->emailBLPose = $emailBLPose;

        return $this;
    }

    /**
     * Get emailBLPose
     *
     * @return string
     */
    public function getEmailBLPose()
    {
        return $this->emailBLPose;
    }

    /**
     * Set signatureBat
     *
     * @param string $signatureBat
     *
     * @return SocieteInfosPlans
     */
    public function setSignatureBat($signatureBat)
    {
        $this->signatureBat = $signatureBat;

        return $this;
    }

    /**
     * Get signatureBat
     *
     * @return string
     */
    public function getSignatureBat()
    {
        return $this->signatureBat;
    }

    /**
     * Set noShippingCosts
     *
     * @param boolean $noShippingCosts
     *
     * @return SocieteInfosPlans
     */
    public function setNoShippingCosts($noShippingCosts)
    {
        $this->noShippingCosts = $noShippingCosts;

        return $this;
    }

    /**
     * Get noShippingCosts
     *
     * @return boolean
     */
    public function getNoShippingCosts()
    {
        return $this->noShippingCosts;
    }

    /**
     * Set shippingCostsByQtyPlans
     *
     * @param boolean $shippingCostsByQtyPlans
     *
     * @return SocieteInfosPlans
     */
    public function setShippingCostsByQtyPlans($shippingCostsByQtyPlans)
    {
        $this->shippingCostsByQtyPlans = $shippingCostsByQtyPlans;

        return $this;
    }

    /**
     * Get shippingCostsByQtyPlans
     *
     * @return boolean
     */
    public function getShippingCostsByQtyPlans()
    {
        return $this->shippingCostsByQtyPlans;
    }

    /**
     * Set societe
     *
     * @param \AW\DoliBundle\Entity\Societe $societe
     *
     * @return SocieteInfosPlans
     */
    public function setSociete(\AW\DoliBundle\Entity\Societe $societe = null)
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
}
