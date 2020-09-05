<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommandeFacture
 *
 * @ORM\Table(name="llx_element_element")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\CommandeFactureRepository")
 */
class CommandeFacture
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
     * @ORM\Column(name="sourcetype", type="string", length=32)
     */
    private $sourcetype;

    /**
     * @var string
     *
     * @ORM\Column(name="targettype", type="string", length=32)
     */
    private $targettype;

    /**
     * @var Commande
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Commande", inversedBy="coFactures")
     * @ORM\JoinColumn(name="fk_source", referencedColumnName="rowid")
     */
    private $commande;

    /**
     * @var Facture
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Facture")
     * @ORM\JoinColumn(name="fk_target", referencedColumnName="rowid")
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
     * Set sourcetype
     *
     * @param string $sourcetype
     *
     * @return CommandeFacture
     */
    public function setSourcetype($sourcetype)
    {
        $this->sourcetype = $sourcetype;

        return $this;
    }

    /**
     * Get sourcetype
     *
     * @return string
     */
    public function getSourcetype()
    {
        return $this->sourcetype;
    }

    /**
     * Set targettype
     *
     * @param string $targettype
     *
     * @return CommandeFacture
     */
    public function setTargettype($targettype)
    {
        $this->targettype = $targettype;

        return $this;
    }

    /**
     * Get targettype
     *
     * @return string
     */
    public function getTargettype()
    {
        return $this->targettype;
    }

    /**
     * Set commande
     *
     * @param \AW\DoliBundle\Entity\Commande $commande
     *
     * @return CommandeFacture
     */
    public function setCommande(\AW\DoliBundle\Entity\Commande $commande = null)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \AW\DoliBundle\Entity\Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set facture
     *
     * @param \AW\DoliBundle\Entity\Facture $facture
     *
     * @return CommandeFacture
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
