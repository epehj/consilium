<?php

namespace AW\DoliBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityManager;

use AW\DoliBundle\Entity\User;

class UserProvider implements UserProviderInterface
{
  private $em;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  public function loadUserByUsername($username)
  {
    $user = $this
      ->em
      ->getRepository('AWDoliBundle:User')
      ->loadUserByUsername($username)
    ;

    if($user === null){
      throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
    }

    return $user;
  }

  public function refreshUser(UserInterface $user)
  {
    if(!$user instanceof User){
      throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
    }

    return $this->loadUserByUsername($user->getUsername());
  }

  public function supportsClass($class)
  {
    return User::class === $class;
  }
}
