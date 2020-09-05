<?php

namespace AW\PlansBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Expedition
 *
 * @ORM\Table(name="aw_expeditionplan")
 * @ORM\Entity(repositoryClass="AW\PlansBundle\Repository\ExpeditionRepository")
 */
class Expedition
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="method", type="integer")
     */
    private $method;

    /**
     * @var string
     *
     * @ORM\Column(name="method_info", type="text", nullable=true)
     */
    private $methodInfo;

    /**
     * @var Commande
     *
     * @ORM\OneToMany(targetEntity="AW\PlansBundle\Entity\Commande", mappedBy="expedition")
     */
    private $commandes;

    const METHOD_NONE = 0;
    const METHOD_CHRONOPOST = 1;
    const METHOD_TNT = 2;

    public function __construct()
    {
      $this->commandes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getTrackingNumbers()
    {
      if($this->method == self::METHOD_CHRONOPOST){
        $methodInfo = unserialize($this->methodInfo);
        if(is_array($methodInfo->return->resultParcelValue)){
          $trackingNumbers = array();
          foreach($methodInfo->return->resultParcelValue as $parcel){
            $trackingNumbers[] = $parcel->skybillNumber;
          }
          return $trackingNumbers;
        }else{
          return array($methodInfo->return->resultParcelValue->skybillNumber);
        }
      }elseif($this->method == self::METHOD_TNT){
        $methodInfo = unserialize($this->methodInfo);
        if(is_array($methodInfo->Expedition->parcelResponses)){
          $trackingNumbers = array();
          foreach($methodInfo->Expedition->parcelResponses as $parcel){
            $trackingNumbers[] = $parcel->parcelNumber;
          }
          return $trackingNumbers;
        }else{
          return array($methodInfo->Expedition->parcelResponses->parcelNumber);
        }
      }

      return null;
    }

    public function getSuiviURL()
    {
      $trackingNumbers = implode(',', $this->getTrackingNumbers());

      if($this->method == self::METHOD_CHRONOPOST){
        return "http://www.chronopost.fr/expedier/inputLTNumbersNoJahia.do?listeNumeros=".$trackingNumbers;
      }elseif($this->method == self::METHOD_TNT){
        return "https://www.tnt.fr/public/suivi_colis/recherche/visubontransport.do?bonTransport=".$trackingNumbers;
      }

      return null;
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Expedition
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
     * Set method
     *
     * @param integer $method
     *
     * @return Expedition
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return int
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set methodInfo
     *
     * @param string $methodInfo
     *
     * @return Expedition
     */
    public function setMethodInfo($methodInfo)
    {
        $this->methodInfo = $methodInfo;

        return $this;
    }

    /**
     * Get methodInfo
     *
     * @return string
     */
    public function getMethodInfo()
    {
        return $this->methodInfo;
    }

    /**
     * Add commande
     *
     * @param \AW\PlansBundle\Entity\Commande $commande
     *
     * @return Expedition
     */
    public function addCommande(\AW\PlansBundle\Entity\Commande $commande)
    {
        $this->commandes[] = $commande;

        return $this;
    }

    /**
     * Remove commande
     *
     * @param \AW\PlansBundle\Entity\Commande $commande
     */
    public function removeCommande(\AW\PlansBundle\Entity\Commande $commande)
    {
        $this->commandes->removeElement($commande);
    }

    /**
     * Get commandes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommandes()
    {
        return $this->commandes;
    }
}
