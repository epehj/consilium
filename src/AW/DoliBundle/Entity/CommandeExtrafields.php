<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommandeExtrafields
 *
 * @ORM\Table(name="llx_commande_extrafields")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\CommandeExtrafieldsRepository")
 */
class CommandeExtrafields
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
     * @ORM\Column(name="affected_wh", type="text", nullable=true)
     */
    private $affectedWh;

    /**
     * @var Commande
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\Commande", inversedBy="extrafields")
     * @ORM\JoinColumn(name="fk_object", referencedColumnName="rowid")
     */
    private $commande;

    const WH_GENAS = 1;

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
     * Set affectedWh
     *
     * @param string $affectedWh
     *
     * @return CommandeExtrafields
     */
    public function setAffectedWh($affectedWh)
    {
        $this->affectedWh = $affectedWh;

        return $this;
    }

    /**
     * Get affectedWh
     *
     * @return string
     */
    public function getAffectedWh()
    {
        return $this->affectedWh;
    }

    /**
     * Set commande
     *
     * @param \AW\DoliBundle\Entity\Commande $commande
     *
     * @return CommandeExtrafields
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
}
