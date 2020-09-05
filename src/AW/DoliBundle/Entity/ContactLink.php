<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactLink
 *
 * @ORM\Table(name="llx_element_contact")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\ContactLinkRepository")
 */
class ContactLink
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
     * @var \DateTime
     *
     * @ORM\Column(name="datecreate", type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var int
     *
     * @ORM\Column(name="statut", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="element_id", type="integer")
     */
    private $elementId;

    /**
     * @var int
     *
     * @ORM\Column(name="fk_c_type_contact", type="integer")
     */
    private $typeContact;

    const TYPE_COMMANDE_SHIPPING = 102;

    /**
     * @var Contact
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Contact", inversedBy="links")
     * @ORM\JoinColumn(name="fk_socpeople", referencedColumnName="rowid")
     */
    private $contact;

    public function __construct()
    {
      $this->dateCreation = new \DateTime();
      $this->status = 4;
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return ContactLink
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
     * Set status
     *
     * @param integer $status
     *
     * @return ContactLink
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
     * Set elementId
     *
     * @param integer $elementId
     *
     * @return ContactLink
     */
    public function setElementId($elementId)
    {
        $this->elementId = $elementId;

        return $this;
    }

    /**
     * Get elementId
     *
     * @return int
     */
    public function getElementId()
    {
        return $this->elementId;
    }

    /**
     * Set typeContact
     *
     * @param integer $typeContact
     *
     * @return ContactLink
     */
    public function setTypeContact($typeContact)
    {
        $this->typeContact = $typeContact;

        return $this;
    }

    /**
     * Get typeContact
     *
     * @return int
     */
    public function getTypeContact()
    {
        return $this->typeContact;
    }

    /**
     * Set contact
     *
     * @param \AW\DoliBundle\Entity\Contact $contact
     *
     * @return ContactLink
     */
    public function setContact(\AW\DoliBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \AW\DoliBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }
}
