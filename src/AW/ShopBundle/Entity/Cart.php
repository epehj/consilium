<?php

namespace AW\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table(name="aw_shop_cart")
 * @ORM\Entity(repositoryClass="AW\ShopBundle\Repository\CartRepository")
 */
class Cart
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
     * @var int
     *
     * @ORM\Column(name="qty", type="integer")
     */
    private $qty;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Product")
     * @ORM\JoinColumn(name="fk_product", referencedColumnName="rowid", nullable=false)
     */
    private $product;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\User")
     * @ORM\JoinColumn(name="fk_user", referencedColumnName="rowid", nullable=false)
     */
    protected $user;

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
     * Set qty
     *
     * @param integer $qty
     *
     * @return Cart
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get qty
     *
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set product
     *
     * @param \AW\DoliBundle\Entity\Product $product
     *
     * @return Cart
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
     * Set user
     *
     * @param \AW\DoliBundle\Entity\User $user
     *
     * @return Cart
     */
    public function setUser(\AW\DoliBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AW\DoliBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
