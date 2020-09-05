<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactExtrafields
 *
 * @ORM\Table(name="llx_socpeople_extrafields")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\ContactExtrafieldsRepository")
 */
class ContactExtrafields
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
     * @ORM\Column(name="type_contact", type="text", nullable=true)
     */
    private $typeContact;

    /**
     * @var string
     *
     * @ORM\Column(name="note_livraison", type="text", nullable=true)
     */
    private $noteLivraison;

    /**
     * @var string
     *
     * @ORM\Column(name="part_livraison", type="text", nullable=true)
     */
    private $partLivraison;

    /**
     * @var Contact
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\Contact", inversedBy="extrafields")
     * @ORM\JoinColumn(name="fk_object", referencedColumnName="rowid")
     */
    private $contact;

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
     * Set typeContact
     *
     * @param string $typeContact
     *
     * @return ContactExtrafields
     */
    public function setTypeContact($typeContact)
    {
        $this->typeContact = $typeContact;

        return $this;
    }

    /**
     * Get typeContact
     *
     * @return string
     */
    public function getTypeContact()
    {
        return $this->typeContact;
    }

    /**
     * Set noteLivraison
     *
     * @param string $noteLivraison
     *
     * @return ContactExtrafields
     */
    public function setNoteLivraison($noteLivraison)
    {
        $this->noteLivraison = $noteLivraison;

        return $this;
    }

    /**
     * Get noteLivraison
     *
     * @return string
     */
    public function getNoteLivraison()
    {
        return $this->noteLivraison;
    }

    /**
     * Set partLivraison
     *
     * @param string $partLivraison
     *
     * @return ContactExtrafields
     */
    public function setPartLivraison($partLivraison)
    {
        $this->partLivraison = $partLivraison;

        return $this;
    }

    /**
     * Get partLivraison
     *
     * @return string
     */
    public function getPartLivraison()
    {
        return $this->partLivraison;
    }

    /**
     * Set contact
     *
     * @param \AW\DoliBundle\Entity\Contact $contact
     *
     * @return ContactExtrafields
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
