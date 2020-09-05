<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="llx_product")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\ProductRepository")
 */
class Product
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
     * @ORM\Column(name="ref", type="string", length=128, unique=true)
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="tva_tx", type="decimal", precision=6, scale=3, nullable=true)
     */
    private $tvaTx;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $costPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="pmp", type="decimal", precision=24, scale=8)
     */
    private $pmp;

    /**
     * @var ProductExtrafields
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\ProductExtrafields", mappedBy="product")
     */
    private $extrafields;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AW\DoliBundle\Entity\Category", mappedBy="products")
     */
    private $categories;

    public function __construct()
    {
      $this->price = 0;
      $this->tvaTx = 0;
      $this->costPrice = 0;
      $this->pmp = 0;
      $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set ref
     *
     * @param string $ref
     *
     * @return Product
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * Set price
     *
     * @param string $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set tvaTx
     *
     * @param string $tvaTx
     *
     * @return Product
     */
    public function setTvaTx($tvaTx)
    {
        $this->tvaTx = $tvaTx;

        return $this;
    }

    /**
     * Get tvaTx
     *
     * @return string
     */
    public function getTvaTx()
    {
        return $this->tvaTx;
    }

    /**
     * Set costPrice
     *
     * @param string $costPrice
     *
     * @return Product
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    /**
     * Get costPrice
     *
     * @return string
     */
    public function getCostPrice()
    {
        return $this->costPrice;
    }

    /**
     * Set pmp
     *
     * @param string $pmp
     *
     * @return Product
     */
    public function setPmp($pmp)
    {
        $this->pmp = $pmp;

        return $this;
    }

    /**
     * Get pmp
     *
     * @return string
     */
    public function getPmp()
    {
        return $this->pmp;
    }

    /**
     * Set extrafields
     *
     * @param \AW\DoliBundle\Entity\ProductExtrafields $extrafields
     *
     * @return Product
     */
    public function setExtrafields(\AW\DoliBundle\Entity\ProductExtrafields $extrafields = null)
    {
        $this->extrafields = $extrafields;

        return $this;
    }

    /**
     * Get extrafields
     *
     * @return \AW\DoliBundle\Entity\ProductExtrafields
     */
    public function getExtrafields()
    {
        return $this->extrafields;
    }

    /**
     * Add category
     *
     * @param \AW\DoliBundle\Entity\Category $category
     *
     * @return Product
     */
    public function addCategory(\AW\DoliBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \AW\DoliBundle\Entity\Category $category
     */
    public function removeCategory(\AW\DoliBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
