<?php

namespace AW\DoliBundle\EventListener;

use Doctrine\ORM\EntityManager;
use AW\DoliBundle\Entity\User;
use AW\DoliBundle\Entity\RightsDef;

class UserRightsManager
{
  protected $em;

  public function __construct(\Doctrine\ORM\EntityManager $em)
  {
    $this->em = $em;
  }

	public function updateRights(User $user)
	{
		$rightsDef = $this->em->getRepository('AWDoliBundle:RightsDef')->findAll();

		// set all rights to false
		$rights = new \stdClass();
		foreach($rightsDef as $r){
      $module = $r->getModule();

			if(!isset($rights->{$module})){
				$rights->$module = new \stdClass();
			}

			$perms = $r->getPerms();
			$subperms = $r->getSubperms();
			if($subperms){
				if(!isset($rights->{$module}->{$perms})){
					$rights->{$module}->{$perms} = new \stdClass();
				}

				$rights->{$module}->{$perms}->{$subperms} = false;
			}else{
				$rights->{$module}->{$perms} = false;
			}
		}

		// set user rights
		foreach($user->getRightsDef() as $r){
			$module = $r->getModule();
			$perms = $r->getPerms();
			$subperms = $r->getSubperms();
			if($subperms){
				$rights->{$module}->{$perms}->{$subperms} = true;
			}else{
				$rights->{$module}->{$perms} = true;
			}
		}

		// set usergroups rights
		foreach($user->getGroups() as $group){
			foreach($group->getRightsDef() as $r){
				$module = $r->getModule();
				$perms = $r->getPerms();
				$subperms = $r->getSubperms();
				if($subperms){
					$rights->{$module}->{$perms}->{$subperms} = true;
				}else{
					$rights->{$module}->{$perms} = true;
				}
			}
		}

		$user->updateRights($rights);
	}
}
