<?php

namespace AW\DoliBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use AW\DoliBundle\EventListener\UserRightsManager;

class UserAccessListener extends DefaultAuthenticationSuccessHandler
{
	protected $httpUtils;
  protected $rightsManager;

	public function __construct(HttpUtils $httpUtils, UserRightsManager $rightsManager)
	{
		$this->httpUtils = $httpUtils;
    $this->rightsManager = $rightsManager;
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token)
	{
    $user = $token->getUser();
    $this->rightsManager->updateRights($user);

    if(!$user->rights->webappli->login){
      throw new AuthenticationException('Access denied.');
    }

    if($user->getSociete() !== null and !$user->getSociete()->getStatus()){
      throw new AuthenticationException('Access denied.');
    }

    // compatibility with legacy authentification
    $_SESSION['user'] = $user->getId();

		return parent::onAuthenticationSuccess($request, $token);
	}
}
