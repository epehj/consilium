<?php

namespace AW\CoreBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class DeviceDetect
{
  private $requestStack;
  private $twig;

  public function __construct(RequestStack $requestStack, TwigEngine $twig)
  {
    $this->requestStack = $requestStack;
    $this->twig = $twig;
  }

  public function blockIE(GetResponseEvent $event)
  {
    if(!$event->isMasterRequest()){
      return;
    }

    $userAgent = $this->requestStack->getCurrentRequest()->headers->get('user-agent');
    if(preg_match('/MSIE/i', $userAgent) or preg_match('/Trident/i', $userAgent) or preg_match('/Edge/i', $userAgent)){
      $content = $this->twig->render('AWCoreBundle::blockIE.html.twig');
      $response = new Response($content);
      $event->setResponse($response);
      $event->stopPropagation();
    }
  }

  public function infosIE(FilterResponseEvent $event)
  {
    if(!$event->isMasterRequest()){
      return;
    }

    $response = $event->getResponse();
    if($response instanceof BinaryFileResponse or $response instanceof JsonResponse){
      return;
    }

    $userAgent = $this->requestStack->getCurrentRequest()->headers->get('user-agent');
    if(preg_match('/MSIE/i', $userAgent) or preg_match('/Trident/i', $userAgent) or preg_match('/Edge/i', $userAgent)){
      $warningHTML = '<div class="alert alert-warning">';
      $warningHTML.= '  <span class="pficon pficon-warning-triangle-o"></span>';
      $warningHTML.= '    Pour profiter pleinement des fonctionnalités de ce site, nous déconseillons l\'utilisation d\'Internet Explorer.';
      $warningHTML.= '    Nous vous recommandons d\'utiliser <a href="https://www.mozilla.org/fr/firefox">Mozilla Firefox</a> ou';
      $warningHTML.= '    <a href="https://www.google.com/chrome">Google Chrome</a>.';
      $warningHTML.= '</div>';

      if(preg_match('/container-fluid/i', $response->getContent())){
        $containerDiv = '<div class="container-fluid">';
      }else{
        $containerDiv = '<div class="container">';
      }

      $content = str_replace(
        $containerDiv,
        $containerDiv.$warningHTML,
        $response->getContent()
      );

      $response->setContent($content);
      return $response;
    }
  }
}
