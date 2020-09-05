<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Propal
 *
 * @ORM\Table(name="llx_propal")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\PropalRepository")
 */
class Propal
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
     * @ORM\Column(name="ref_client", type="string", length=255, nullable=true)
     */
    private $refClient;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin_validite", type="datetime", nullable=true)
     */
    private $finValidite;

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

    public function __construct()
    {
      $this->status = 0;
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
     * @return Propal
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
     * @return Propal
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
     * Set finValidite
     *
     * @param \DateTime $finValidite
     *
     * @return Propal
     */
    public function setFinValidite($finValidite)
    {
        $this->finValidite = $finValidite;

        return $this;
    }

    /**
     * Get finValidite
     *
     * @return \DateTime
     */
    public function getFinValidite()
    {
        return $this->finValidite;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Propal
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
     * @return Propal
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
