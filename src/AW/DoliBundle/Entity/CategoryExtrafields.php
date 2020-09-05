<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryExtrafields
 *
 * @ORM\Table(name="llx_categories_extrafields")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\CategoryExtrafieldsRepository")
 */
class CategoryExtrafields
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
     * @var bool
     *
     * @ORM\Column(name="available_online", type="boolean", nullable=true)
     */
    private $availableOnline;

    /**
     * @var Category
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\Category", inversedBy="extrafields")
     * @ORM\JoinColumn(name="fk_object", referencedColumnName="rowid")
     */
    private $category;

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
     * Set availableOnline
     *
     * @param boolean $availableOnline
     *
     * @return CategoryExtrafields
     */
    public function setAvailableOnline($availableOnline)
    {
        $this->availableOnline = $availableOnline;

        return $this;
    }

    /**
     * Get availableOnline
     *
     * @return bool
     */
    public function getAvailableOnline()
    {
        return $this->availableOnline;
    }

    /**
     * Set category
     *
     * @param \AW\DoliBundle\Entity\Category $category
     *
     * @return CategoryExtrafields
     */
    public function setCategory(\AW\DoliBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AW\DoliBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
