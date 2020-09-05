<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RightsDef
 *
 * @ORM\Table(name="llx_rights_def")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\RightsDefRepository")
 */
class RightsDef
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
     * @ORM\Column(name="module", type="string", length=64, nullable=true)
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Column(name="perms", type="string", length=50, nullable=true)
     */
    private $perms;

    /**
     * @var string
     *
     * @ORM\Column(name="subperms", type="string", length=50, nullable=true)
     */
    private $subperms;


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
     * Set module
     *
     * @param string $module
     *
     * @return RightsDef
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set perms
     *
     * @param string $perms
     *
     * @return RightsDef
     */
    public function setPerms($perms)
    {
        $this->perms = $perms;

        return $this;
    }

    /**
     * Get perms
     *
     * @return string
     */
    public function getPerms()
    {
        return $this->perms;
    }

    /**
     * Set subperms
     *
     * @param string $subperms
     *
     * @return RightsDef
     */
    public function setSubperms($subperms)
    {
        $this->subperms = $subperms;

        return $this;
    }

    /**
     * Get subperms
     *
     * @return string
     */
    public function getSubperms()
    {
        return $this->subperms;
    }
}

