<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usergroup
 *
 * @ORM\Table(name="llx_usergroup")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\UsergroupRepository")
 */
class Usergroup
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
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AW\DoliBundle\Entity\User", mappedBy="groups")
     */
    private $users;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AW\DoliBundle\Entity\RightsDef")
     * @ORM\JoinTable(name="llx_usergroup_rights",
     *                  joinColumns={@ORM\JoinColumn(name="fk_usergroup", referencedColumnName="rowid")},
     *                  inverseJoinColumns={@ORM\JoinColumn(name="fk_id", referencedColumnName="id")}
     * )
     */
    private $rightsDef;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Usergroup
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
     * Add user
     *
     * @param \AW\DoliBundle\Entity\User $user
     *
     * @return Usergroup
     */
    public function addUser(\AW\DoliBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AW\DoliBundle\Entity\User $user
     */
    public function removeUser(\AW\DoliBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add rightsDef
     *
     * @param \AW\DoliBundle\Entity\RightsDef $rightsDef
     *
     * @return Usergroup
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
}
