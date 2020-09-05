<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Societe
 *
 * @ORM\Table(name="llx_societe")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\SocieteRepository")
 */
class Societe
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
     * @ORM\Column(name="nom", type="string", length=128, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code_client", type="string", length=24, nullable=true, unique=true)
     */
    private $codeClient;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string", length=255, nullable=true, unique=true)
     */
    private $barcode;

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
     * @ORM\JoinColumn(name="fk_pays", referencedColumnName="rowid")
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=128, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="client", type="integer")
     */
    private $client;

    /**
     * @var bool
     *
     * @ORM\Column(name="customer_bad", type="integer", nullable=true)
     */
    private $customerBad;

    /**
     * @var int
     *
     * @ORM\Column(name="cond_reglement", type="integer", nullable=true)
     */
    private $condReglement;

    /**
     * @var int
     *
     * @ORM\Column(name="mode_reglement", type="integer", nullable=true)
     */
    private $modeReglement;

    /**
     * @var Societe
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Societe", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="rowid")
     */
    private $parent;

    /**
     * @var SocieteInfosPlans
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\SocieteInfosPlans", mappedBy="societe")
     */
    private $infosPlans;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AW\DoliBundle\Entity\Societe", mappedBy="parent")
     */
    private $children;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinTable(name="llx_societe_commerciaux",
     *                  joinColumns={@ORM\JoinColumn(name="fk_soc", referencedColumnName="rowid")},
     *                  inverseJoinColumns={@ORM\JoinColumn(name="fk_user", referencedColumnName="rowid")}
     * )
     */
    private $commercials;

    /*
     * Note couleur client
     */
    const CUSTOMER_BAD_GREEN = 0;
    const CUSTOMER_BAD_ORANGE = 10;
    const CUSTOMER_BAD_BLUE = 20;
    const CUSTOMER_BAD_PURPLE = 25;
    const CUSTOMER_BAD_RED = 30;
    const CUSTOMER_BAD_BLACK = 40;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AW\DoliBundle\Entity\Contact", mappedBy="societe")
     */
    private $contacts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->commercials = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function getLinkTo()
    {
      return '/comm/card.php?socid='.$this->id;
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
     * Set name
     *
     * @param string $name
     *
     * @return Societe
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set codeClient
     *
     * @param string $codeClient
     *
     * @return Societe
     */
    public function setCodeClient($codeClient)
    {
        $this->codeClient = $codeClient;

        return $this;
    }

    /**
     * Get codeClient
     *
     * @return string
     */
    public function getCodeClient()
    {
        return $this->codeClient;
    }

    /**
     * Set barcode
     *
     * @param string $barcode
     *
     * @return Societe
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * Get barcode
     *
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Societe
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
     * @return Societe
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
     * @return Societe
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
     * @return Societe
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
     * Set email
     *
     * @param string $email
     *
     * @return Societe
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
     * Set phone
     *
     * @param string $phone
     *
     * @return Societe
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
     * Set logo
     *
     * @param string $logo
     *
     * @return Societe
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Societe
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
     * Set client
     *
     * @param integer $client
     *
     * @return Societe
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return int
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set customerBad
     *
     * @param integer $customerBad
     *
     * @return Societe
     */
    public function setCustomerBad($customerBad)
    {
        $this->customerBad = $customerBad;

        return $this;
    }

    /**
     * Get customerBad
     *
     * @return int
     */
    public function getCustomerBad()
    {
        return $this->customerBad;
    }

    /**
     * Set condReglement
     *
     * @param integer $condReglement
     *
     * @return Societe
     */
    public function setCondReglement($condReglement)
    {
        $this->condReglement = $condReglement;

        return $this;
    }

    /**
     * Get condReglement
     *
     * @return integer
     */
    public function getCondReglement()
    {
        return $this->condReglement;
    }

    /**
     * Set modeReglement
     *
     * @param integer $modeReglement
     *
     * @return Societe
     */
    public function setModeReglement($modeReglement)
    {
        $this->modeReglement = $modeReglement;

        return $this;
    }

    /**
     * Get modeReglement
     *
     * @return integer
     */
    public function getModeReglement()
    {
        return $this->modeReglement;
    }

    /**
     * Set parent
     *
     * @param \AW\DoliBundle\Entity\Societe $parent
     *
     * @return Societe
     */
    public function setParent(\AW\DoliBundle\Entity\Societe $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AW\DoliBundle\Entity\Societe
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \AW\DoliBundle\Entity\Societe $child
     *
     * @return Societe
     */
    public function addChild(\AW\DoliBundle\Entity\Societe $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AW\DoliBundle\Entity\Societe $child
     */
    public function removeChild(\AW\DoliBundle\Entity\Societe $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set infosPlans
     *
     * @param \AW\DoliBundle\Entity\SocieteInfosPlans $infosPlans
     *
     * @return Societe
     */
    public function setInfosPlans(\AW\DoliBundle\Entity\SocieteInfosPlans $infosPlans = null)
    {
        $this->infosPlans = $infosPlans;

        return $this;
    }

    /**
     * Get infosPlans
     *
     * @return \AW\DoliBundle\Entity\SocieteInfosPlans
     */
    public function getInfosPlans()
    {
        return $this->infosPlans;
    }

    /**
     * Add commercial
     *
     * @param \AW\DoliBundle\Entity\User $commercial
     *
     * @return Societe
     */
    public function addCommercial(\AW\DoliBundle\Entity\User $commercial)
    {
        $this->commercials[] = $commercial;

        return $this;
    }

    /**
     * Remove commercial
     *
     * @param \AW\DoliBundle\Entity\User $commercial
     */
    public function removeCommercial(\AW\DoliBundle\Entity\User $commercial)
    {
        $this->commercials->removeElement($commercial);
    }

    /**
     * Get commercials
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommercials()
    {
        return $this->commercials;
    }

    /**
     * Add contact
     *
     * @param \AW\DoliBundle\Entity\Contact $contact
     *
     * @return Societe
     */
    public function addContact(\AW\DoliBundle\Entity\Contact $contact)
    {
        $this->contacts[] = $contact;

        return $this;
    }

    /**
     * Remove contact
     *
     * @param \AW\DoliBundle\Entity\Contact $contact
     */
    public function removeContact(\AW\DoliBundle\Entity\Contact $contact)
    {
        $this->contacts->removeElement($contact);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }
}
