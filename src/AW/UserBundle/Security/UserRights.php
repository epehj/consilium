<?php

namespace AW\UserBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

use AW\DoliBundle\EventListener\UserRightsManager;
use AW\DoliBundle\Entity\User;

/**
 * doc http://symfony.com/doc/current/security/voters.html
 *
 * les droits sont sous forme de chaine "module.perm.subperm"
 * Dans les controlleurs, utiliser la methode denyAccessUnlessGranted ou isGranted
 * Dans les templates, utiliser is_granted
 */
class UserRights extends Voter
{
  private $decisionManager;
  private $rightsManager;

  public function __construct(AccessDecisionManagerInterface $decisionManager, UserRightsManager $rightsManager)
  {
    $this->decisionManager = $decisionManager;
    $this->rightsManager = $rightsManager;
  }

  /**
   * Seuls les droits de base sans indication de l'objet est supportés
   */
  protected function supports($attribute, $subject)
  {
    if($subject == null){
      return true;
    }else{
      return false;
    }
  }

  protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
  {
    $user = $token->getUser();

    if($attribute == 'ROLE_ALLOWED_TO_SWITCH'){
      if($user->getSociete()){
        return false;
      }

      if($this->decisionManager->decide($token, array('ROLE_PREVIOUS_ADMIN'))){
        return false;
      }

      return $this->voteOnAttribute('webappli.admin', null, $token);
    }

    // perms for internal user only
    if($user->getSociete() and in_array($attribute, array(
      'webappli.admin',
      'webappli.showcaplan',
      'webappli.cmdplan.seeprod',
      'webappli.cmdplan.see_expedition',
      'webappli.cmdplan.see_facturation'
    ))){
      return false;
    }

    // $attribute doit être sous la forme module.perm.subperm
    $r = explode('.', $attribute, 3);

    // seul subperm est facultatif. La taille minimum du tableau est donc de 2.
    if(count($r) < 2){
      return false;
    }

    if($user->rights === null){ // charger les droits si nécessaire uniquement
      $this->rightsManager->updateRights($user);
    }

    if(count($r) == 2 and isset($user->rights->{$r[0]}->{$r[1]})){
      return $user->rights->{$r[0]}->{$r[1]};
    }elseif(isset($user->rights->{$r[0]}->{$r[1]}->{$r[2]})){
      return $user->rights->{$r[0]}->{$r[1]}->{$r[2]};
    }

    return false;
  }
}
