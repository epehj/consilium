<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaiementFacture
 *
 * @ORM\Table(name="llx_paiement_facture")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\PaiementFactureRepository")
 */
class PaiementFacture
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
     * @ORM\Column(name="amount", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $amount;

    /**
     * @var Facture
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Facture", inversedBy="paiements")
     * @ORM\JoinColumn(name="fk_facture", referencedColumnName="rowid")
     */
    private $facture;

    public function __construct()
    {
      $this->amount = 0;
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
     * Set amount
     *
     * @param string $amount
     *
     * @return PaiementFacture
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set facture
     *
     * @param \AW\DoliBundle\Entity\Facture $facture
     *
     * @return PaiementFacture
     */
    public function setFacture(\AW\DoliBundle\Entity\Facture $facture = null)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return \AW\DoliBundle\Entity\Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }
}
