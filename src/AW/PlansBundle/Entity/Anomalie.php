<?php


namespace AW\PlansBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Commande
 *
 * @ORM\Table(name="aw_anomalie")
 * @ORM\Entity(repositoryClass="AW\PlansBundle\Repository\AnomalieRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="id")
 */
class Anomalie
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
     * @ORM\Column(name="label", type="string")
     * @Assert\NotNull
     */
    protected $label;

    /**
     * var Commande
     * @ORM\ManyToMany(targetEntity="AW\PlansBundle\Entity\Commande", mappedBy="anomalies")
     */
    protected $commandes;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * @param mixed $commandes
     */
    public function setCommandes($commandes)
    {
        $this->commandes = $commandes;
    }

}