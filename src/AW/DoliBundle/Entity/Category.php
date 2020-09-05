<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="llx_categorie")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\Column(name="label", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Category", inversedBy="children")
     * @ORM\JoinColumn(name="fk_parent", referencedColumnName="rowid")
     */
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AW\DoliBundle\Entity\Category", mappedBy="parent")
     */
    private $children;

    /**
     * @var CategoryExtrafields
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\CategoryExtrafields", mappedBy="category")
     */
    private $extrafields;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AW\DoliBundle\Entity\Product", inversedBy="categories")
     * @ORM\JoinTable(name="llx_categorie_product",
     *                joinColumns={@ORM\JoinColumn(name="fk_categorie", referencedColumnName="rowid")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="fk_product", referencedColumnName="rowid")}
     * )
     */
    private $products;

    public function __construct()
    {
      $this->children = new \Doctrine\Common\Collections\ArrayCollection();
      $this->products = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Category
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
     * Set type
     *
     * @param integer $type
     *
     * @return Category
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set parent
     *
     * @param \AW\DoliBundle\Entity\Category $parent
     *
     * @return Category
     */
    public function setParent(\AW\DoliBundle\Entity\Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AW\DoliBundle\Entity\Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \AW\DoliBundle\Entity\Category $child
     *
     * @return Category
     */
    public function addChild(\AW\DoliBundle\Entity\Category $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AW\DoliBundle\Entity\Category $child
     */
    public function removeChild(\AW\DoliBundle\Entity\Category $child)
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
     * Set extrafields
     *
     * @param \AW\DoliBundle\Entity\CategoryExtrafields $extrafields
     *
     * @return Category
     */
    public function setExtrafields(\AW\DoliBundle\Entity\CategoryExtrafields $extrafields = null)
    {
        $this->extrafields = $extrafields;

        return $this;
    }

    /**
     * Get extrafields
     *
     * @return \AW\DoliBundle\Entity\CategoryExtrafields
     */
    public function getExtrafields()
    {
        return $this->extrafields;
    }

    /**
     * Add product
     *
     * @param \AW\DoliBundle\Entity\Product $product
     *
     * @return Category
     */
    public function addProduct(\AW\DoliBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \AW\DoliBundle\Entity\Product $product
     */
    public function removeProduct(\AW\DoliBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
