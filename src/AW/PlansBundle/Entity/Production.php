<?php

namespace AW\PlansBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Production
 *
 * @ORM\Table(name="aw_commandeplanprod")
 * @ORM\Entity(repositoryClass="AW\PlansBundle\Repository\ProductionRepository")
 */
class Production
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
     * @var int
     *
     * @ORM\Column(name="status_commande", type="integer")
     * @Assert\Type("integer")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_start", type="datetime")
     * @Assert\DateTime()
     */
    private $dateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateEnd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_ack", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateAck;

    /**
     * @var Commande
     *
     * @ORM\ManyToOne(targetEntity="AW\PlansBundle\Entity\Commande", inversedBy="productions")
     * @ORM\JoinColumn(name="fk_commande", referencedColumnName="rowid", nullable=false)
     * @Assert\Valid()
     */
    private $commande;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user", referencedColumnName="rowid", nullable=false)
     * @Assert\Valid()
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_ack", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    private $userAck;

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
     * Set status
     *
     * @param integer $status
     *
     * @return Production
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
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return Production
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return Production
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set dateAck
     *
     * @param \DateTime $dateAck
     *
     * @return Production
     */
    public function setDateAck($dateAck)
    {
        $this->dateAck = $dateAck;

        return $this;
    }

    /**
     * Get dateAck
     *
     * @return \DateTime
     */
    public function getDateAck()
    {
        return $this->dateAck;
    }

    /**
     * Set commande
     *
     * @param \AW\PlansBundle\Entity\Commande $commande
     *
     * @return Production
     */
    public function setCommande(\AW\PlansBundle\Entity\Commande $commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \AW\PlansBundle\Entity\Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set user
     *
     * @param \AW\DoliBundle\Entity\User $user
     *
     * @return Production
     */
    public function setUser(\AW\DoliBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set userAck
     *
     * @param \AW\DoliBundle\Entity\User $userAck
     *
     * @return Production
     */
    public function setUserAck(\AW\DoliBundle\Entity\User $userAck = null)
    {
        $this->userAck = $userAck;

        return $this;
    }

    /**
     * Get userAck
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserAck()
    {
        return $this->userAck;
    }
}
