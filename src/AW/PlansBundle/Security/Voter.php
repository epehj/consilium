<?php

namespace AW\PlansBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter as VoterBase;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

use AW\DoliBundle\Entity\Societe;
use AW\PlansBundle\Entity\Commande;

class Voter extends VoterBase
{
  private $decisionManager;

  public function __construct(AccessDecisionManagerInterface $decisionManager)
  {
    $this->decisionManager = $decisionManager;
  }

  protected function supports($attribute, $subject)
  {
    if($subject instanceof Commande){
      return true;
    }else{
      return false;
    }
  }

  protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
  {
    switch($attribute){
      case 'VIEW':
        return $this->canView($subject, $token);
      case 'VIEW_FILE':
        return $this->canViewFile($subject, $token);
      case 'VIEW_SUIVI':
        return $this->canViewSuivi($subject, $token);
      case 'VIEW_MAIL':
        return $this->canViewMail($subject, $token);
      case 'EDIT':
        return $this->canEdit($subject, $token);
      case 'EDIT_INTERNAL_USER':
        return $this->canEditOnlyInternalUser($subject, $token);
      case 'VALIDATE':
        return $this->canValidate($subject, $token);
      case 'SEND_BAT':
        return $this->canSendBAT($subject, $token);
      case 'MODIFY_BAT':
        return $this->canModifyBAT($subject, $token);
      case 'SEND_PRINTER':
        return $this->canSendToPrinter($subject, $token);
      case 'RECEIVE':
        return $this->canReceive($subject, $token);
      case 'REOPEN':
        return $this->canReOpen($subject, $token);
      case 'DELETE':
        return $this->canDelete($subject, $token);
      case 'CLONE':
        return $this->canClone($subject, $token);
      case 'CLOSED':
        return $this->canClosed($subject, $token);
      case 'VIEW_PRODUCTION':
        return $this->canViewProduction($subject, $token);
      case 'START_PRODUCTION':
        return $this->canStartProduction($subject, $token);
      case 'CANCEL_PRODUCTION':
        return $this->canCancelProduction($subject, $token);
      case 'END_PRODUCTION':
        return $this->canEndProduction($subject, $token);
      case 'RETURN_PRODUCTION':
        return $this->canReturnProduction($subject, $token);
      case 'VIEW_RELEVE':
        return $this->canViewReleve($subject, $token);
      case 'ADD_RELEVE':
        return $this->canAddReleve($subject, $token);
      case 'STACK_RELEVE':
        return $this->canStackReleve($subject, $token);
      case 'RELEVE_ANOMALIE':
        return $this->canMarkReleveAnomalie($subject, $token);
      case 'STACK_POSE':
        return $this->canStackPose($subject, $token);
      case 'POSE_DONE':
        return $this->canMarkPoseDone($subject, $token);
      case 'VIEW_NOTE_PRIVATE':
        return $this->canViewNotePrivate($subject, $token);
      case 'VIEW_NOTE_PUBLIC':
        return $this->canViewNotePublic($subject, $token);
    }

    throw new \LogicException($attribute.' : Droit inconnu!');
  }

  private function canView(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if(!$user->getSociete() and
      ($this->decisionManager->decide($token, array('webappli.cmdplan.sendbat')) or
      $this->decisionManager->decide($token, array('webappli.cmdplan.see_expedition')))
    ){
      return true;
    }

    if(!$this->decisionManager->decide($token, array('webappli.cmdplan.see'))){
      return false;
    }

    if(!$user->getSociete()){
      return true;
    }

    if($user->getSociete() == $commande->getSociete()){
      return true;
    }

    foreach($user->getSociete()->getChildren() as $child){
      if($commande->getSociete() == $child){
        return true;
      }
    }

    return false;
  }

  private function canViewFile(Commande $commande, TokenInterface $token)
  {
    if(!$token->getUser()->getSociete()){
      return true;
    }

    return $this->canView($commande, $token);
  }

  private function canViewSuivi(Commande $commande, TokenInterface $token)
  {
    if($token->getUser()->getSociete()){
      return false;
    }

    return $this->canView($commande, $token);
  }

  private function canViewMail(Commande $commande, TokenInterface $token)
  {
    if($this->canView($commande, $token)){
      return true;
    }

    return $this->canViewProduction($commande, $token);
  }

  private function canEdit(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if(!$this->decisionManager->decide($token, array('webappli.cmdplan.modify'))){
      return false;
    }

    if(!$commande->isCanBeModify()){
      return false;
    }

    if(!$user->getSociete()){
      return true;
    }

    if($user->getSociete() == $commande->getSociete()){
      return true;
    }

    foreach($user->getSociete()->getChildren() as $child){
      if($commande->getSociete() == $child){
        return true;
      }
    }

    return false;
  }

  private function canEditOnlyInternalUser(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if(!$this->decisionManager->decide($token, array('webappli.cmdplan.modify'))){
      return false;
    }

    return $commande->isCanBeModify();
  }

  private function canValidate(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if($commande->getSociete()->getCustomerBad() == Societe::CUSTOMER_BAD_BLACK){
      return false;
    }

    if(!$this->decisionManager->decide($token, array('webappli.cmdplan.chgstatut'))){
      return false;
    }

    return $commande->isCanBeValidate();
  }

  private function canSendBAT(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if(!$commande->isCanBeSentBAT()){
      return false;
    }

    if($this->decisionManager->decide($token, array('webappli.cmdplan.chgstatut'))){
      return true;
    }

    if($this->decisionManager->decide($token, array('webappli.cmdplan.sendbat')) and
      !$commande->getBatOnlyByFr() and
      (!$commande->getSociete()->getInfosPlans() or !$commande->getSociete()->getInfosPlans()->getBatOnlyByFr())
    ){
      return true;
    }

    return false;
  }

  private function canModifyBAT(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if(!$this->decisionManager->decide($token, array('webappli.cmdplan.chgstatut'))){
      return false;
    }

    if($commande->getStatus() != $commande::STATUS_BAT){
      return false;
    }

    if(!$user->getSociete()){
      return true;
    }

    if($user->getSociete() == $commande->getSociete()){
      return true;
    }

    foreach($user->getSociete()->getChildren() as $child){
      if($commande->getSociete() == $child){
        return true;
      }
    }

    return false;
  }

  private function canSendToPrinter(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete() or $commande->getSociete()->getCustomerBad() == Societe::CUSTOMER_BAD_RED){
      return false;
    }

    if(!$this->decisionManager->decide($token, array('webappli.cmdplan.chgstatut'))){
      return false;
    }

    return $commande->isCanBePrinted();
  }

  private function canReceive(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if($commande->getStatus() != Commande::STATUS_EN_FABRICATION){
      return false;
    }

    if(
      $this->decisionManager->decide($token, array('webappli.cmdplan.chgstatut')) or
      $this->decisionManager->decide($token, array('webappli.cmdplan.see_expedition'))
    ){
      return true;
    }

    return false;
  }

  private function canReOpen(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if(!$commande->isCanceled()){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.chgstatut'));
  }

  private function canDelete(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if(!$commande->isCanBeModify()){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.delete'));
  }

  private function canClone(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.new'));
  }

  private function canClosed(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($commande->getStatus() != Commande::STATUS_EN_EXPEDITION){
      return false;
    }

    if($user->getSociete()){
      return true;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.chgstatut'));
  }

  private function canViewProduction(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.prod'));
  }

  private function canStartProduction(Commande $commande, TokenInterface $token)
  {
    if(!$this->canViewProduction($commande, $token)){
      return false;
    }

    if($commande->getReleve() and $commande->getReleveStatus() != Commande::RELEVE_STATUS_TERMINE){
      return false;
    }

    if($commande->getStatus() != Commande::STATUS_VALIDATED and
      $commande->getStatus() != Commande::STATUS_BAT_MODIF and
      $commande->getStatus() != Commande::STATUS_BAT_VALIDATED
    ){
      return false;
    }

    return $commande->getProduction() == 1 ? true : false;
  }

  private function canCancelProduction(Commande $commande, TokenInterface $token)
  {
    if($token->getUser()->getSociete()){
      return false;
    }

    if($commande->getProduction() != 1){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.send2prod'));
  }

  private function canEndProduction(Commande $commande, TokenInterface $token)
  {
    if(!$this->canViewProduction($commande, $token)){
      return false;
    }

    if($commande->getProduction() != 2){
      return false;
    }

    return $commande->getUserProduction() == $token->getUser() ? true : false;
  }

  private function canReturnProduction(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if($commande->getStatus() != Commande::STATUS_VALIDATED and
      $commande->getStatus() != Commande::STATUS_BAT_MODIF and
      $commande->getStatus() != Commande::STATUS_BAT_VALIDATED
    ){
      return false;
    }

    if($commande->getProduction() != 3){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.send2prod'));
  }

  private function canViewReleve(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if(!$commande->getReleve() and !$commande->getPose()){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.releves'));
  }

  private function canAddReleve(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if($commande->isWaiting() or
      $commande->isCanceled() or
      !$commande->getReleve() or
      $commande->getReleveStatus() != Commande::RELEVE_STATUS_EN_ATTENTE
    ){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.releves'));
  }

  private function canStackReleve(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if($commande->getStatus() != Commande::STATUS_VALIDATED or
      !$commande->getReleve() or
      ($commande->getReleveStatus() != Commande::RELEVE_STATUS_TERMINE and $commande->getReleveStatus() != Commande::RELEVE_ANOMALIE)
    ){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.releves'));
  }

  private function canMarkReleveAnomalie(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if($commande->isWaiting() or
      $commande->isCanceled() or
      (!$commande->getReleve() and !$commande->getPose()) or
      ($commande->getReleveStatus() != Commande::RELEVE_STATUS_EN_ATTENTE and $commande->getReleveStatus() != Commande::POSE_STATUS_EN_ATTENTE)
    ){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.releves'));
  }

  private function canMarkPoseDone(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if(!$commande->getPose() or $commande->getReleveStatus() != Commande::POSE_STATUS_EN_ATTENTE){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.releves'));
  }

  private function canStackPose(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if($commande->getStatus() != Commande::STATUS_RECEIVED or
      !$commande->getPose() or
      ($commande->getReleveStatus() != Commande::RELEVE_ANOMALIE and $commande->getReleveStatus() != Commande::RELEVE_ANOMALIE)
    ){
      return false;
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.releves'));
  }

  private function canViewNotePrivate(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return false;
    }

    if($this->canView($commande, $token)){
      return true;
    }

    return $this->canViewProduction($commande, $token);
  }

  private function canViewNotePublic(Commande $commande, TokenInterface $token)
  {
    $user = $token->getUser();

    if($user->getSociete()){
      return $this->canView($commande, $token);
    }

    return $this->decisionManager->decide($token, array('webappli.cmdplan.see'));
  }
}
