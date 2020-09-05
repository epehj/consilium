<?php

namespace AW\PlansBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bat
 *
 * @ORM\Table(name="aw_commandeplanbat")
 * @ORM\Entity(repositoryClass="AW\PlansBundle\Repository\BatRepository")
 */
class Bat
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
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime", nullable=true)
     */
    private $dateModification;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_validation", type="datetime", nullable=true)
     */
    private $dateValidation;

    /**
     * @var Commande
     *
     * @ORM\ManyToOne(targetEntity="AW\PlansBundle\Entity\Commande", inversedBy="bats")
     * @ORM\JoinColumn(name="fk_commande", referencedColumnName="rowid", nullable=false)
     */
    private $commande;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_creation", referencedColumnName="rowid", nullable=false)
     */
    protected $userCreation;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_modification", referencedColumnName="rowid")
     */
    protected $userModification;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_validation", referencedColumnName="rowid")
     */
    protected $userValidation;

    public function __construct()
    {
      $this->dateCreation = new \DateTime();
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
     * Set numero
     *
     * @param integer $numero
     *
     * @return Bat
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Bat
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
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return Bat
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set dateValidation
     *
     * @param \DateTime $dateValidation
     *
     * @return Bat
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
     * Set commande
     *
     * @param \AW\PlansBundle\Entity\Commande $commande
     *
     * @return Bat
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
     * Set userCreation
     *
     * @param \AW\DoliBundle\Entity\User $userCreation
     *
     * @return Bat
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
     * Set userModification
     *
     * @param \AW\DoliBundle\Entity\User $userModification
     *
     * @return Bat
     */
    public function setUserModification(\AW\DoliBundle\Entity\User $userModification = null)
    {
        $this->userModification = $userModification;

        return $this;
    }

    /**
     * Get userModification
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUserModification()
    {
        return $this->userModification;
    }

    /**
     * Set userValidation
     *
     * @param \AW\DoliBundle\Entity\User $userValidation
     *
     * @return Bat
     */
    public function setUserValidation(\AW\DoliBundle\Entity\User $userValidation = null)
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
}
