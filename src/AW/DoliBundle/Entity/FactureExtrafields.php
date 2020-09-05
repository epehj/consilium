<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactureExtrafields
 *
 * @ORM\Table(name="llx_facture_extrafields")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\FactureExtrafieldsRepository")
 */
class FactureExtrafields
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
     * @ORM\Column(name="no_relance_auto", type="boolean", nullable=true)
     */
    private $noRelanceAuto;

    /**
     * @var Facture
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\Facture", inversedBy="extrafields")
     * @ORM\JoinColumn(name="fk_object", referencedColumnName="rowid")
     */
    private $facture;

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
     * Set noRelanceAuto
     *
     * @param boolean $noRelanceAuto
     *
     * @return FactureExtrafields
     */
    public function setNoRelanceAuto($noRelanceAuto)
    {
        $this->noRelanceAuto = $noRelanceAuto;

        return $this;
    }

    /**
     * Get noRelanceAuto
     *
     * @return bool
     */
    public function getNoRelanceAuto()
    {
        return $this->noRelanceAuto;
    }

    /**
     * Set facture
     *
     * @param \AW\DoliBundle\Entity\Facture $facture
     *
     * @return FactureExtrafields
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
