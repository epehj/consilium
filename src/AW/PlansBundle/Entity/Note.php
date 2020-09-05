<?php

namespace AW\PlansBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Note
 *
 * @ORM\Table(name="aw_commandeplan_note")
 * @ORM\Entity(repositoryClass="AW\PlansBundle\Repository\NoteRepository")
 */
class Note
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
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="private", type="boolean")
     */
    private $private;

    /**
     * @var Commande
     *
     * @ORM\ManyToOne(targetEntity="AW\PlansBundle\Entity\Commande", inversedBy="notes")
     * @ORM\JoinColumn(name="fk_commande", referencedColumnName="rowid", nullable=false)
     */
    private $commande;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user", referencedColumnName="rowid", nullable=false)
     */
    protected $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * @var bool
     *
     * @ORM\Column(name="seen", type="boolean")
     */
    private $seen;

    public function __construct()
    {
      $this->date = new \DateTime();
      $this->private = true;
      $this->seen = false;
    }

    public function isPublic()
    {
      return !$this->private;
    }

    public function isPrivate()
    {
      return $this->private;
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
     * Set message
     *
     * @param string $message
     *
     * @return Note
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Note
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set private
     *
     * @param boolean $private
     *
     * @return Note
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * Get private
     *
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Set commande
     *
     * @param \AW\PlansBundle\Entity\Commande $commande
     *
     * @return Note
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
     * @return Note
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
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return Note
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set seen
     *
     * @param boolean $seen
     *
     * @return Note
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * Get seen
     *
     * @return boolean
     */
    public function getSeen()
    {
        return $this->seen;
    }
}
