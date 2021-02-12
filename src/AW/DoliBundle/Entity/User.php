<?php

namespace AW\DoliBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User
 *
 * @ORM\Table(name="llx_user")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\UserRepository")
 */
class User implements AdvancedUserInterface
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
     * @ORM\Column(name="login", type="string", length=50, nullable=true, unique=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="pass_crypted", type="string", length=128, nullable=true)
     */
    private $passCrypted;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=128, nullable=true)
     */
    private $apiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=50, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=50, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="signature", type="text", nullable=true)
     */
    private $signature;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=25, nullable=true)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=50, nullable=true)
     */
    private $town;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Country")
     * @ORM\JoinColumn(name="fk_country", referencedColumnName="rowid")
     */
    private $country;

    /**
     * @var bool
     *
     * @ORM\Column(name="admin", type="boolean")
     */
    private $admin;

    /**
     * @var int
     *
     * @ORM\Column(name="statut", type="integer")
     */
    private $status;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AW\DoliBundle\Entity\Usergroup", inversedBy="users")
     * @ORM\JoinTable(name="llx_usergroup_user",
     *                joinColumns={@ORM\JoinColumn(name="fk_user", referencedColumnName="rowid")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="fk_usergroup", referencedColumnName="rowid")}
     * )
     */
    private $groups;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AW\DoliBundle\Entity\RightsDef")
     * @ORM\JoinTable(name="llx_user_rights",
     *                  joinColumns={@ORM\JoinColumn(name="fk_user", referencedColumnName="rowid")},
     *                  inverseJoinColumns={@ORM\JoinColumn(name="fk_id", referencedColumnName="id")}
     * )
     */
    private $rightsDef;
    public $rights;

    /**
     * @var Societe
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Societe")
     * @ORM\JoinColumn(name="fk_soc", referencedColumnName="rowid", nullable=true)
     */
    private $societe;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user", referencedColumnName="rowid", nullable=true)
     */
    private $manager;

    /**
     * @var UserParam
     *
     * @ORM\OneToOne(targetEntity="AW\UserBundle\Entity\UserParam", mappedBy="user")
     */
    private $param;

    /**
     * @var Commande
     *
     * @ORM\OneToMany(targetEntity="AW\UserBundle\Entity\UserParam", mappedBy="releveur",cascade="all", orphanRemoval=true))
     */
    private $commandesRelevees;

    /**
     * @var Commande
     *
     * @ORM\OneToMany(targetEntity="AW\UserBundle\Entity\UserParam", mappedBy="poseur", cascade="all", orphanRemoval=true))
     */
    private $commandesPosees;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rightsDef = new \Doctrine\Common\Collections\ArrayCollection();
        $this->commandesPosees = \Doctrine\Common\Collections\ArrayCollection();
        $this->commandesRelevees = \Doctrine\Common\Collections\ArrayCollection();
        $this->rights = null;
    }

    /**
     * @return ArrayCollection
     */
    public function getCommandesPosees()
    {
        return $this->commandesPosees;
    }

    /**
     * @param ArrayCollection $commandePosees
     */
    public function addCommandePosees($commandePosees)
    {
        if(!$this->commandePosees->contains($commandePosees))
            $this->commandePosees[] = $commandePosees;
        return $this;
    }
    /**
     * @param ArrayCollection $anomalies
     */
    public function removeCommandePosees($commandePosees)
    {
        if($this->commandePosees->contains($commandePosees))
            $this->commandePosees->removeElement($commandePosees);
        return $this;
    }
    /**
     * @return ArrayCollection
     */
    public function getCommandesRelevees()
    {
        return $this->commandesRelevees;
    }

    /**
     * @param ArrayCollection $commandeRelevees
     */
    public function addCommandeRelevees($commandeRelevees)
    {
        if(!$this->commandeRelevees->contains($commandeRelevees))
            $this->commandeRelevees[] = $commandeRelevees;
        return $this;
    }
    /**
     * @param ArrayCollection $anomalies
     */
    public function removeCommandeRelevees($commandeRelevees)
    {
        if($this->commandeRelevees->contains($commandeRelevees))
            $this->commandeRelevees->removeElement($commandeRelevees);
        return $this;
    }

    /**
     * Get fullname (firstname lastname)
     *
     * @return string
     */
    public function getFullName()
    {
      return trim($this->firstname.' '.$this->lastname);
    }

    public function getLinkTo()
    {
      return '/user/card.php?id='.$this->id;
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
     * Set login
     *
     * @param string $login
     *
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set passCrypted
     *
     * @param string $passCrypted
     *
     * @return User
     */
    public function setPassCrypted($passCrypted)
    {
        $this->passCrypted = $passCrypted;

        return $this;
    }

    /**
     * Get passCrypted
     *
     * @return string
     */
    public function getPassCrypted()
    {
        return $this->passCrypted;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set signature
     *
     * @param string $signature
     *
     * @return User
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    public function getAddress1()
    {
        $address = explode("\n", $this->address, 2);
        return trim($address[0]);
    }

    public function getAddress2()
    {
        $address = explode("\n", $this->address, 2);
        $address2 = isset($address[1]) ? trim($address[1]) : '';
        $address2 = str_replace(array("\r\n", "\n", "\r"), ' ', $address2);
        return trim($address2);
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return User
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set town
     *
     * @param string $town
     *
     * @return User
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set country
     *
     * @param \AW\DoliBundle\Entity\Country $country
     *
     * @return User
     */
    public function setCountry(\AW\DoliBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \AW\DoliBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set admin
     *
     * @param boolean $admin
     *
     * @return User
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return bool
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
      return $this->admin;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return User
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
     * Add group
     *
     * @param \AW\DoliBundle\Entity\Usergroup $group
     *
     * @return User
     */
    public function addGroup(\AW\DoliBundle\Entity\Usergroup $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param \AW\DoliBundle\Entity\Usergroup $group
     */
    public function removeGroup(\AW\DoliBundle\Entity\Usergroup $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /*******************************************************************
     * AdvancedUserInterface methods
     ******************************************************************/
    /**
     * Get username (login here)
     *
     * @return string
     */
    public function getUsername()
    {
      return $this->login;
    }

    /**
     * Get password (passCrypt here)
     *
     * @return string
     */
    public function getPassword()
    {
      return $this->passCrypted;
    }

    /**
     * Get salt
     */
    public function getSalt()
    {
      return null;
    }

    public function eraseCredentials(){}

    /**
     * Pas d'implémentation d'expiratin de compte
     *
     * @return true
     */
    public function isAccountNonExpired()
    {
      return true;
    }

    /**
     * Pas d'implémentation de compte blocqué
     *
     * @return true
     */
    public function isAccountNonLocked()
    {
      return true;
    }

    /**
     * Pas d'implémentation d'expiration de compte
     *
     * @return true
     */
    public function isCredentialsNonExpired()
    {
      return true;
    }

    /**
     * Get status if is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
      return $this->status;
    }

    /**
     * Get roles
     *
     * All users is ROLE_USER
     */
    public function getRoles()
    {
      if($this->admin){
        return array('ROLE_SUPER_ADMIN');
      }else{
        return array('ROLE_USER');
      }
    }

    /**
     * Add rightsDef
     *
     * @param \AW\DoliBundle\Entity\RightsDef $rightsDef
     *
     * @return User
     */
    public function addRightsDef(\AW\DoliBundle\Entity\RightsDef $rightsDef)
    {
        $this->rightsDef[] = $rightsDef;

        return $this;
    }

    /**
     * Remove rightsDef
     *
     * @param \AW\DoliBundle\Entity\RightsDef $rightsDef
     */
    public function removeRightsDef(\AW\DoliBundle\Entity\RightsDef $rightsDef)
    {
        $this->rightsDef->removeElement($rightsDef);
    }

    /**
     * Get rightsDef
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRightsDef()
    {
        return $this->rightsDef;
    }

    /**
     * Update user perms
     */
    public function updateRights(\stdClass $rights)
    {
      $this->rights = $rights;
    }

    /**
     * Set societe
     *
     * @param \AW\DoliBundle\Entity\Societe $societe
     *
     * @return User
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
     * Set manager
     *
     * @param \AW\DoliBundle\Entity\User $manager
     *
     * @return User
     */
    public function setManager(\AW\DoliBundle\Entity\User $manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set param
     *
     * @param \AW\UserBundle\Entity\UserParam $param
     *
     * @return User
     */
    public function setParam(\AW\UserBundle\Entity\UserParam $param = null)
    {
        $this->param = $param;

        return $this;
    }

    /**
     * Get param
     *
     * @return \AW\UserBundle\Entity\UserParam
     */
    public function getParam()
    {
        return $this->param;
    }
}
