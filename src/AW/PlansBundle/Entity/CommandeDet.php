<?php

namespace AW\PlansBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * CommandeDet
 *
 * @ORM\Table(name="aw_commandeplandet")
 * @ORM\Entity(repositoryClass="AW\PlansBundle\Repository\CommandeDetRepository")
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
     * @ORM\Column(name="type", type="string", length=2)
     * @Assert\Length(min=2,max=2)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="qty", type="integer")
     * @Assert\NotNull()
     * @Assert\GreaterThanOrEqual(1)
     */
    private $qty;

    /**
     * @var string
     *
     * @ORM\Column(name="format", type="string", length=10, nullable=true)
     * @Assert\Length(min=2,max=2)
     */
    private $format;

    /**
     * @var string
     *
     * @ORM\Column(name="matiere", type="string", length=10, nullable=true)
     * @Assert\Length(min=2,max=3)
     */
    private $matiere;

    /**
     * @var string
     *
     * @ORM\Column(name="design", type="string", length=10, nullable=true)
     * @Assert\Length(min=2,max=4)
     */
    private $design;

    /**
     * @var string
     *
     * @ORM\Column(name="vue", type="string", length=10, nullable=true)
     * @Assert\Length(min=1,max=1)
     */
    private $vue;

    /**
     * @var string
     *
     * @ORM\Column(name="finition", type="string", length=10, nullable=true)
     * @Assert\Length(min=1,max=3)
     */
    private $finition;

    /**
     * @var bool
     *
     * @ORM\Column(name="hole", type="boolean")
     * @Assert\Type("bool")
     */
    private $hole;

    /**
     * @var string
     *
     * @ORM\Column(name="background", type="string", length=10, nullable=true)
     * @Assert\Length(min=1, max=2)
     */
    private $background;

    /**
     * @var string
     *
     * @ORM\Column(name="remise", type="decimal", precision=24, scale=8)
     * @Assert\Type("numeric")
     * @Assert\GreaterThanOrEqual(0)
     */
    private $remise;

    /**
     * @var Commande
     *
     * @ORM\ManyToOne(targetEntity="AW\PlansBundle\Entity\Commande", inversedBy="listDet")
     * @ORM\JoinColumn(name="fk_commande", referencedColumnName="rowid", nullable=false)
     * @Assert\Valid()
     */
    private $commande;

    public static $typeName = [
      'PE' => "Plan d'Évacuation",
      'PI' => "Plan d'Intervention",
      'PC' => "Plan de Chambre",
      'PM' => "Plan de Masse"
    ];

    public static $formatName = [
      'A4' => "A4",
      'A3' => "A3",
      'A2' => "A2",
      'A1' => "A1",
      'A0' => "A0"
    ];

    public static $matiereName = [
      'PDF' => "PDF",
      'PVC' => "Forex",
      'DB'  => "Dibond Blanc",
      'DAB' => "Dibond Alu Brosse",
      'DOR' => "Dibond Or",
      'PX'  => "Plexy",
      'AL'  => "Altu",
      'PL'  => "Photoluminescent",
      'PR'  => "Plastification Rigide - PVC aouéé",
    ];

    public static $designName = [
      '2D'    => "2D",
      '3D'    => "3D",
      '2D3D'  => "2D+3D"
    ];

    public static $vueName = [
      'A' => "À l'appréciation du dessinateur",
      'H' => "Horizontal",
      'V' => "Vertical"
    ];

    public static $finitionName = [
      '0'   => "Aucune",
      'CT'  => "Cadre Tradi.",
      'CCC' => "Cadre Clic-clac FullOpen",
      'CVC' => "Cadre Clic-Clac Classic",
      'CM'  => "Cadre Clic-Clac Premium",
      'EN'  => "Entretoises",
    ];

    public static $backgroundName = [
      'T'   => "Transparent",
      'BS'  => "Blanc de soutien"
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->qty = 1;
      $this->remise = 0;
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
     * Set type
     *
     * @param string $type
     *
     * @return CommandeDet
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set qty
     *
     * @param integer $qty
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
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set format
     *
     * @param string $format
     *
     * @return CommandeDet
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set matiere
     *
     * @param string $matiere
     *
     * @return CommandeDet
     */
    public function setMatiere($matiere)
    {
        $this->matiere = $matiere;

        return $this;
    }

    /**
     * Get matiere
     *
     * @return string
     */
    public function getMatiere()
    {
        return $this->matiere;
    }

    /**
     * Set design
     *
     * @param string $design
     *
     * @return CommandeDet
     */
    public function setDesign($design)
    {
        $this->design = $design;

        return $this;
    }

    /**
     * Get design
     *
     * @return string
     */
    public function getDesign()
    {
        return $this->design;
    }

    /**
     * Set vue
     *
     * @param string $vue
     *
     * @return CommandeDet
     */
    public function setVue($vue)
    {
        $this->vue = $vue;

        return $this;
    }

    /**
     * Get vue
     *
     * @return string
     */
    public function getVue()
    {
        return $this->vue;
    }

    /**
     * Set finition
     *
     * @param string $finition
     *
     * @return CommandeDet
     */
    public function setFinition($finition)
    {
        $this->finition = $finition;

        return $this;
    }

    /**
     * Get finition
     *
     * @return string
     */
    public function getFinition()
    {
        return $this->finition;
    }

    /**
     * Set hole
     *
     * @param boolean $hole
     *
     * @return CommandeDet
     */
    public function setHole($hole)
    {
        $this->hole = $hole;

        return $this;
    }

    /**
     * Get hole
     *
     * @return boolean
     */
    public function getHole()
    {
        return $this->hole;
    }

    /**
     * Set background
     *
     * @param string $background
     *
     * @return CommandeDet
     */
    public function setBackground($background)
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Get background
     *
     * @return string
     */
    public function getBackground()
    {
        return $this->background;
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
     * Set commande
     *
     * @param \AW\PlansBundle\Entity\Commande $commande
     *
     * @return CommandeDet
     */
    public function setCommande(\AW\PlansBundle\Entity\Commande $commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \AW\PlansBundle\Entity\Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Get type name
     *
     * @return string
     */
    public function getTypeName()
    {
      if(isset(self::$typeName[$this->type])){
        return self::$typeName[$this->type];
      }else{
        return '';
      }
    }

    /**
     * Get format name
     *
     * @return string
     */
    public function getFormatName()
    {
      if(isset(self::$formatName[$this->format])){
        return self::$formatName[$this->format];
      }else{
        return '';
      }
    }

    /**
     * Get matiere name
     *
     * @return string
     */
    public function getMatiereName()
    {
      if(isset(self::$matiereName[$this->matiere])){
        return self::$matiereName[$this->matiere];
      }else{
        return '';
      }
    }

    /**
     * Get design name
     *
     * @return string
     */
    public function getDesignName()
    {
      if(isset(self::$designName[$this->design])){
        return self::$designName[$this->design];
      }else{
        return '';
      }
    }

    /**
     * Get vue name
     *
     * @return string
     */
    public function getVueName()
    {
      if(isset(self::$vueName[$this->vue])){
        return self::$vueName[$this->vue];
      }else{
        return '';
      }
    }

    /**
     * @Assert\Callback
     */
    public function isValid(ExecutionContextInterface $context)
    {
      if($this->type == 'PM' and $this->design !== null){
        $context
          ->buildViolation('Le design ne doit pas être renseigner pour un plan de masse.')
          ->atPath('design')
          ->addViolation()
        ;
      }

      if($this->type != 'PM' and $this->design === null){
        $context
          ->buildViolation('Le design est obligatoire.')
          ->atPath('design')
          ->addViolation()
        ;
      }
    }

    /**
     * Get finition name
     *
     * @return string
     */
    public function getFinitionName()
    {
      if(isset(self::$finitionName[$this->finition])){
        return self::$finitionName[$this->finition];
      }else{
        return '';
      }
    }

    /**
     * Get background name
     *
     * @return string
     */
    public function getBackgroundName()
    {
      if(isset(self::$backgroundName[$this->background])){
        return self::$backgroundName[$this->background];
      }else{
        return '';
      }
    }

    public function getMainProductRef()
    {
      if($this->type == 'PM'){
        return $this->type.$this->format.$this->matiere;
      }else{
        return $this->type.$this->format.$this->matiere.$this->design;
      }
    }

    public function getFinitionProductRef()
    {
      if(!is_string($this->finition)){
        return null;
      }

      switch($this->finition){
        case 'CT':
          return 'CPA'.$this->format[1];
          break;
        case 'CCC': // anciennement Clic-Clack FullOpen
        case 'CVC':
          return 'CVC'.$this->format;
          break;
        case 'CM':
          return 'CM'.$this->format;
          break;
        case 'EN':
          return 'ENT';
          break;
        default:
          return null;
          break;
      }
    }
}
