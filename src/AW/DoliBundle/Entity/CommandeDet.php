<?php

namespace AW\DoliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommandeDet
 *
 * @ORM\Table(name="llx_commandedet")
 * @ORM\Entity(repositoryClass="AW\DoliBundle\Repository\CommandeDetRepository")
 */
class CommandeDet
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
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="qty", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $qty;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="subprice", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $subprice;

    /**
     * @var string
     *
     * @ORM\Column(name="tva_tx", type="decimal", precision=6, scale=3, nullable=true)
     */
    private $tvaTx;

    /**
     * @var string
     *
     * @ORM\Column(name="total_ht", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $totalHt;

    /**
     * @var string
     *
     * @ORM\Column(name="total_tva", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $totalTva;

    /**
     * @var string
     *
     * @ORM\Column(name="total_ttc", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $totalTtc;

    /**
     * @var string
     *
     * @ORM\Column(name="remise_percent", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $remisePercent;

    /**
     * @var string
     *
     * @ORM\Column(name="remise", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $remise;

    /**
     * @var string
     *
     * @ORM\Column(name="buy_price_ht", type="decimal", precision=24, scale=8, nullable=true)
     */
    private $buyPriceHt;

    /**
     * @var Commande
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Commande", inversedBy="listDet")
     * @ORM\JoinColumn(name="fk_commande", referencedColumnName="rowid", nullable=false)
     */
    private $commande;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="AW\DoliBundle\Entity\Product")
     * @ORM\JoinColumn(name="fk_product", referencedColumnName="rowid")
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
     * Set name
     *
     * @param string $name
     *
     * @return CommandeDet
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
     * Set description
     *
     * @param string $description
     *
     * @return CommandeDet
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set qty
     *
     * @param string $qty
     *
     * @return CommandeDet
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get qty
     *
     * @return string
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return CommandeDet
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
     * Set subprice
     *
     * @param string $subprice
     *
     * @return CommandeDet
     */
    public function setSubprice($subprice)
    {
        $this->subprice = $subprice;

        return $this;
    }

    /**
     * Get subprice
     *
     * @return string
     */
    public function getSubprice()
    {
        return $this->subprice;
    }

    /**
     * Set tvaTx
     *
     * @param string $tvaTx
     *
     * @return CommandeDet
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
     * Set totalHt
     *
     * @param string $totalHt
     *
     * @return CommandeDet
     */
    public function setTotalHt($totalHt)
    {
        $this->totalHt = $totalHt;

        return $this;
    }

    /**
     * Get totalHt
     *
     * @return string
     */
    public function getTotalHt()
    {
        return $this->totalHt;
    }

    /**
     * Set totalTva
     *
     * @param string $totalTva
     *
     * @return CommandeDet
     */
    public function setTotalTva($totalTva)
    {
        $this->totalTva = $totalTva;

        return $this;
    }

    /**
     * Get totalTva
     *
     * @return string
     */
    public function getTotalTva()
    {
        return $this->totalTva;
    }

    /**
     * Set totalTtc
     *
     * @param string $totalTtc
     *
     * @return CommandeDet
     */
    public function setTotalTtc($totalTtc)
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }

    /**
     * Get totalTtc
     *
     * @return string
     */
    public function getTotalTtc()
    {
        return $this->totalTtc;
    }

    /**
     * Set remisePercent
     *
     * @param string $remisePercent
     *
     * @return CommandeDet
     */
    public function setRemisePercent($remisePercent)
    {
        $this->remisePercent = $remisePercent;

        return $this;
    }

    /**
     * Get remisePercent
     *
     * @return string
     */
    public function getRemisePercent()
    {
        return $this->remisePercent;
    }

    /**
     * Set remise
     *
     * @param string $remise
     *
     * @return CommandeDet
     */
    public function setRemise($remise)
    {
        $this->remise = $remise;

        return $this;
    }

    /**
     * Get remise
     *
     * @return string
     */
    public function getRemise()
    {
        return $this->remise;
    }

    /**
     * Set buyPriceHt
     *
     * @param string $buyPriceHt
     *
     * @return CommandeDet
     */
    public function setBuyPriceHt($buyPriceHt)
    {
        $this->buyPriceHt = $buyPriceHt;

        return $this;
    }

    /**
     * Get buyPriceHt
     *
     * @return string
     */
    public function getBuyPriceHt()
    {
        return $this->buyPriceHt;
    }

    /**
     * Set commande
     *
     * @param \AW\DoliBundle\Entity\Commande $commande
     *
     * @return CommandeDet
     */
    public function setCommande(\AW\DoliBundle\Entity\Commande $commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \AW\DoliBundle\Entity\Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set product
     *
     * @param \AW\DoliBundle\Entity\Product $product
     *
     * @return CommandeDet
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
