<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductExtrafields
 *
 * @ORM\Table(name="llx_product_extrafields")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\ProductExtrafieldsRepository")
 */
class ProductExtrafields
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
     * @var bool
     *
     * @ORM\Column(name="customized_product", type="boolean", nullable=true)
     */
    private $customized;

    /**
     * @var Product
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\Product", inversedBy="extrafields")
     * @ORM\JoinColumn(name="fk_object", referencedColumnName="rowid")
     */
    private $product;

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
     * @return ProductExtrafields
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
     * Set customized
     *
     * @param boolean $customized
     *
     * @return ProductExtrafields
     */
    public function setCustomized($customized)
    {
        $this->customized = $customized;

        return $this;
    }

    /**
     * Get customized
     *
     * @return bool
     */
    public function getCustomized()
    {
        return $this->customized;
    }

    /**
     * Set product
     *
     * @param \AW\DoliBundle\Entity\Product $product
     *
     * @return ProductExtrafields
     */
    public function setProduct(\AW\DoliBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \AW\DoliBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
