<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Facture
 *
 * @ORM\Table(name="llx_facture")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\FactureRepository")
 */
class Facture
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
     * @ORM\Column(name="facnumber", type="string", length=30, unique=true)
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_client", type="string", length=255, nullable=true)
     */
    private $refClient;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datef", type="date", nullable=true)
     */
    private $dateFacture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_lim_reglement", type="date", nullable=true)
     */
    private $dateLimReglement;

    /**
     * @var int
     *
     * @ORM\Column(name="fk_statut", type="smallint")
     */
    private $status;

    /**
     * @var Societe
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Societe")
     * @ORM\JoinColumn(name="fk_soc", referencedColumnName="rowid")
     */
    private $societe;

    /**
     * @var PaiementFacture
     *
     * @ORM\OneToMany(targetEntity="AW\DoliBundle\Entity\PaiementFacture", mappedBy="facture", cascade={"persist", "remove"})
     */
    private $paiements;

    /**
     * @var FactureExtrafields
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\FactureExtrafields", mappedBy="facture")
     */
    private $extrafields;

    public function __construct()
    {
      $this->status = 0;
    }

    /**
     * Get sub link to Dolibarr
     *
     * @return string
     */
    public function getLinkTo()
    {
      return '/compta/facture.php?facid='.$this->id;
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
     * @return Facture
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
     * @return Facture
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
     * Set dateFacture
     *
     * @param \DateTime $dateFacture
     *
     * @return Facture
     */
    public function setDateFacture($dateFacture)
    {
        $this->dateFacture = $dateFacture;

        return $this;
    }

    /**
     * Get dateFacture
     *
     * @return \DateTime
     */
    public function getDateFacture()
    {
        return $this->dateFacture;
    }

    /**
     * Set dateLimReglement
     *
     * @param \DateTime $dateLimReglement
     *
     * @return Facture
     */
    public function setDateLimReglement($dateLimReglement)
    {
        $this->dateLimReglement = $dateLimReglement;

        return $this;
    }

    /**
     * Get dateLimReglement
     *
     * @return \DateTime
     */
    public function getDateLimReglement()
    {
        return $this->dateLimReglement;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Facture
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
     * Set societe
     *
     * @param \AW\DoliBundle\Entity\Societe $societe
     *
     * @return Facture
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

    /**
     * Add paiement
     *
     * @param \AW\DoliBundle\Entity\PaiementFacture $paiement
     *
     * @return Facture
     */
    public function addPaiement(\AW\DoliBundle\Entity\PaiementFacture $paiement)
    {
        $this->paiements[] = $paiement;

        return $this;
    }

    /**
     * Remove paiement
     *
     * @param \AW\DoliBundle\Entity\PaiementFacture $paiement
     */
    public function removePaiement(\AW\DoliBundle\Entity\PaiementFacture $paiement)
    {
        $this->paiements->removeElement($paiement);
    }

    /**
     * Get paiements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaiements()
    {
        return $this->paiements;
    }

    /**
     * Set extrafields
     *
     * @param \AW\DoliBundle\Entity\FactureExtrafields $extrafields
     *
     * @return Facture
     */
    public function setExtrafields(\AW\DoliBundle\Entity\FactureExtrafields $extrafields = null)
    {
        $this->extrafields = $extrafields;

        return $this;
    }

    /**
     * Get extrafields
     *
     * @return \AW\DoliBundle\Entity\FactureExtrafields
     */
    public function getExtrafields()
    {
        return $this->extrafields;
    }
}
