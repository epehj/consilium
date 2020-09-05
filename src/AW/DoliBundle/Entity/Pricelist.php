<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pricelist
 *
 * @ORM\Table(name="llx_pricelist")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\PricelistRepository")
 */
class Pricelist
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
     * @ORM\Column(name="from_qty", type="decimal", precision=24, scale=8)
     */
    private $fromQty;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=24, scale=8)
     */
    private $price;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Product")
     * @ORM\JoinColumn(name="fk_product", referencedColumnName="rowid", nullable=false)
     */
    private $product;

    /**
     * @var Societe
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Societe")
     * @ORM\JoinColumn(name="fk_soc", referencedColumnName="rowid")
     */
    private $societe;

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
     * Set fromQty
     *
     * @param string $fromQty
     *
     * @return Pricelist
     */
    public function setFromQty($fromQty)
    {
        $this->fromQty = $fromQty;

        return $this;
    }

    /**
     * Get fromQty
     *
     * @return string
     */
    public function getFromQty()
    {
        return $this->fromQty;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Pricelist
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
     * Set product
     *
     * @param \AW\DoliBundle\Entity\Product $product
     *
     * @return Pricelist
     */
    public function setProduct(\AW\DoliBundle\Entity\Product $product)
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

    /**
     * Set societe
     *
     * @param \AW\DoliBundle\Entity\Societe $societe
     *
     * @return Pricelist
     */
    public function setSociete(\AW\DoliBundle\Entity\Societe $societe = null)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return \AW\DoliBundle\Entity\Societe
     */
    public function getSociete()
    {
        return $this->societe;
    }
}
