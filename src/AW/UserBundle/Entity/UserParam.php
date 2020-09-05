<?php

namespace AW\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserParam
 *
 * @ORM\Table(name="aw_user_param")
 * @ORM\Entity(repositoryClass="AW\UserBundle\Repository\UserParamRepository")
 */
class UserParam
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
     * @var User
     *
     * @ORM\OneToOne(targetEntity="AW\DoliBundle\Entity\User", inversedBy="param")
     * @ORM\JoinColumn(name="fk_user", referencedColumnName="rowid", nullable=false)
     */
    protected $user;

    /**
     * @var array
     *
     * @ORM\Column(name="plans_datatables_state", type="array", nullable=true)
     */
    private $plansDataTablesState;

    /**
     * @var array
     *
     * @ORM\Column(name="infos_news", type="array", nullable=true)
     */
    private $infosNews;

    public function __construct()
    {
      $this->plansDataTablesState = array();
      $this->infosNews = array();
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
     * Set user
     *
     * @param \AW\DoliBundle\Entity\User $user
     *
     * @return UserParam
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

    /**
     * Set plansDataTablesState
     *
     * @param array $plansDataTablesState
     *
     * @return UserParam
     */
    public function setPlansDataTablesState($plansDataTablesState)
    {
        $this->plansDataTablesState = $plansDataTablesState;

        return $this;
    }

    /**
     * Get plansDataTablesState
     *
     * @return array
     */
    public function getPlansDataTablesState()
    {
        return $this->plansDataTablesState;
    }

    /**
     * Set infosNews
     *
     * @param array $infosNews
     *
     * @return UserParam
     */
    public function setInfosNews($infosNews)
    {
        $this->infosNews = $infosNews;

        return $this;
    }

    /**
     * Get infosNews
     *
     * @return array
     */
    public function getInfosNews()
    {
        return $this->infosNews;
    }
}
