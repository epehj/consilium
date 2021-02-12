<?php

namespace AW\PlansBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TypeBatiment
 *
 * @ORM\Table(name="aw_type_batiment")
 * @ORM\Entity(repositoryClass="AW\PlansBundle\Repository\TypeBatimentRepository")
 */
class TypeBatiment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, unique=true)
     */
    private $type;

    /**
     * @var CommandeST
     *
     * @ORM\OneToMany(targetEntity="AW\PlansBundle\Entity\CommandeST", mappedBy="typeBatiment")
     */
    private $commandesST;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->commandesST = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getCommandesST()
    {
        return $this->commandesST;
    }

    /**
     * @param ArrayCollection $commandesST
     */
    public function addCommandesST($commandeST)
    {
        if(!$this->commandesST->contains($commandeST))
            $this->commandesST[] = $commandeST;
        return $this;
    }
    /**
     * @param ArrayCollection $commandeST
     */
    public function removeCommandesST($commandeST)
    {
        if($this->commandesST->contains($commandeST))
            $this->commandesST->removeElement($commandeST);
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}

