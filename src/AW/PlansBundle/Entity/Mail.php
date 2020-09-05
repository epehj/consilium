<?php

namespace AW\PlansBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mail
 *
 * @ORM\Table(name="aw_commandeplan_mail")
 * @ORM\Entity(repositoryClass="AW\PlansBundle\Repository\MailRepository")
 */
class Mail
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
     * @ORM\Column(name="address_to", type="string", length=255)
     */
    private $addressTo;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

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
     * @var Commande
     *
     * @ORM\ManyToOne(targetEntity="AW\PlansBundle\Entity\Commande", inversedBy="mails")
     * @ORM\JoinColumn(name="fk_commande", referencedColumnName="rowid", nullable=false)
     */
    private $commande;

    public function __construct()
    {
      $this->date = new \DateTime();
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
     * Set addressTo
     *
     * @param string $addressTo
     *
     * @return Mail
     */
    public function setAddressTo($addressTo)
    {
        $this->addressTo = $addressTo;

        return $this;
    }

    /**
     * Get addressTo
     *
     * @return string
     */
    public function getAddressTo()
    {
        return $this->addressTo;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Mail
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Mail
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
     * @return Mail
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
     * Set commande
     *
     * @param \AW\PlansBundle\Entity\Commande $commande
     *
     * @return Mail
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

    private function addressToArray($address)
    {
      if(trim($address) == ''){
        return array();
      }

      $data = explode('<', trim($address), 2);
      if(count($data) > 1){
        return array(
          trim($data[1], '>') => trim($data[0])
        );
      }else{
        return $data;
      }
    }

    public function getAddressToFormated()
    {
      $data = explode(',', $this->addressTo);
      $mails = array();
      foreach($data as $address){
        $mails = array_merge($mails, $this->addressToArray($address));
      }

      return $mails;
    }
}
