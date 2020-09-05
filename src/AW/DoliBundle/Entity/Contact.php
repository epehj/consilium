<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Contact
 *
 * @ORM\Table(name="llx_socpeople")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\ContactRepository")
 */
class Contact
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
     * @ORM\Column(name="civility", type="string", length=6, nullable=true)
     */
    private $civility;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=50, nullable=true)
     * @Assert\Length(min=2,max=50)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=30, nullable=true)
     * @Assert\Length(max=30)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_perso", type="string", length=30, nullable=true)
     * @Assert\Length(max=30)
     */
    private $phonePerso;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_mobile", type="string", length=30, nullable=true)
     * @Assert\Length(max=30)
     */
    private $phoneMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Assert\Length(min=5,max=255)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=25, nullable=true)
     * @Assert\Length(max=25)
     * @Assert\NotBlank()
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     * @Assert\NotBlank()
     */
    private $town;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Country")
     * @ORM\JoinColumn(name="fk_pays", referencedColumnName="rowid")
     * @Assert\Valid()
     */
    protected $country;

    /**
     * @var bool
     *
     * @ORM\Column(name="statut", type="boolean")
     */
    private $status;

    /**
     * @var Societe
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Societe", inversedBy="contacts")
     * @ORM\JoinColumn(name="fk_soc", referencedColumnName="rowid")
     */
    private $societe;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user_creat", referencedColumnName="rowid")
     */
    private $userCreation;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AW\DoliBundle\Entity\ContactLink", mappedBy="contact")
     */
    private $links;

    /**
     * @var ContactExtrafields
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\ContactExtrafields", mappedBy="contact", cascade={"remove"})
     */
    private $extrafields;

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->status = true;
      $this->links = new ArrayCollection();
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

    public function getPhoneOrMobile()
    {
      if($this->phone){
        return $this->phone;
      }elseif($this->phoneMobile){
        return $this->phoneMobile;
      }else{
        return $this->phonePerso;
      }
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
     * Set civility
     *
     * @param string $civility
     *
     * @return Contact
     */
    public function setCivility($civility)
    {
        $this->civility = $civility;

        return $this;
    }

    /**
     * Get civility
     *
     * @return string
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Contact
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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Contact
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
     * Set phone
     *
     * @param string $phone
     *
     * @return Contact
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phonePerso
     *
     * @param string $phonePerso
     *
     * @return Contact
     */
    public function setPhonePerso($phonePerso)
    {
        $this->phonePerso = $phonePerso;

        return $this;
    }

    /**
     * Get phonePerso
     *
     * @return string
     */
    public function getPhonePerso()
    {
        return $this->phonePerso;
    }

    /**
     * Set phoneMobile
     *
     * @param string $phoneMobile
     *
     * @return Contact
     */
    public function setPhoneMobile($phoneMobile)
    {
        $this->phoneMobile = $phoneMobile;

        return $this;
    }

    /**
     * Get phoneMobile
     *
     * @return string
     */
    public function getPhoneMobile()
    {
        return $this->phoneMobile;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Contact
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
     * Set address
     *
     * @param string $address
     *
     * @return Contact
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
        return isset($address[1]) ? trim($address[1]) : '';
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Contact
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
     * @return Contact
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
     * @return Contact
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
     * Set status
     *
     * @param boolean $status
     *
     * @return Contact
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
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
     * @return Contact
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
     * Set userCreation
     *
     * @param \AW\DoliBundle\Entity\User $userCreation
     *
     * @return Contact
     */
    public function setUserCreation(\AW\DoliBundle\Entity\User $userCreation = null)
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
     * Add link
     *
     * @param \AW\DoliBundle\Entity\ContactLink $link
     *
     * @return Contact
     */
    public function addLink(\AW\DoliBundle\Entity\ContactLink $link)
    {
        $this->links[] = $link;

        return $this;
    }

    /**
     * Remove link
     *
     * @param \AW\DoliBundle\Entity\ContactLink $link
     */
    public function removeLink(\AW\DoliBundle\Entity\ContactLink $link)
    {
        $this->links->removeElement($link);
    }

    /**
     * Get links
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set extrafields
     *
     * @param \AW\DoliBundle\Entity\ContactExtrafields $extrafields
     *
     * @return Contact
     */
    public function setExtrafields(\AW\DoliBundle\Entity\ContactExtrafields $extrafields = null)
    {
        $this->extrafields = $extrafields;

        return $this;
    }

    /**
     * Get extrafields
     *
     * @return \AW\DoliBundle\Entity\ContactExtrafields
     */
    public function getExtrafields()
    {
        return $this->extrafields;
    }
}
