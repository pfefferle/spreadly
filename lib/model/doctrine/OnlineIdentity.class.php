<?php

/**
 * OnlineIdentity
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    yiid
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class OnlineIdentity extends BaseOnlineIdentity
{

  protected $aPostApiClient = null;

  /**
   * builds the real profile-url using the identifier
   * @deprecated use $this->getProfileUri() now
   * @return string
   */
  public function getUrl() {
    return $this->getProfileUri();
  }

  public function getOAuthToken(){
  }

  public function getCommunity() {
    return CommunityTable::getInstance()->retrieveByPk($this->getCommunityId());
  }

  public function getName() {
    if ($this->_get("name")) {
      return $this->_get("name");
    } else {
      return $this->getUrl();
    }
  }

  public function scheduleImportJob() {
    if(!$this->getLastFriendRefresh() ||
        $this->getLastFriendRefresh() <
        strtotime(sfConfig::get("app_jobs_last_friend_refresh_threshold"))) {
      Queue\Queue::getInstance()->put(new Documents\ContactImportJob($this->getId()));
    }
  }

  /**
   * Enter description here...
   *
   */
  public function preDelete($event) {
    $user_id = $this->getUserId();

    $lQuery = Doctrine_Query::create()
      ->from('OnlineIdentity oi')
      ->andWhere('oi.user_id = ?', $user_id);

    if ($lQuery->count() < 2) {
      sfContext::getInstance()->getLogger()->notice("you can't delete this online identity");
      throw new sfException("you can't delete your last account");
    }

    sfContext::getInstance()->getLogger()->notice("you can delete this online identity");
  }

  /**
   * check if online identity is used as default avatar provider
   *
   * @param unknown_type $event
   */
  public function preSave($event) {
    $user_id = $this->getUserId();

    if (!$user_id) {
      return;
    }

    $count = Doctrine_Query::create()
      ->from('OnlineIdentity oi')
      ->where('oi.user_id = ?', $user_id)
      ->andWhere('oi.use_as_avatar = ?', true)
      ->count();

    sfContext::getInstance()->getLogger()->notice($user_id);
    if (($count < 1) && $this->getPhoto()) {
      $this->setUseAsAvatar(true);
    }
  }

  public function deactivate() {
    $this->setActive(false);
    $this->save();
  }

  public function useAsAvatar() {
    $user_id = $this->getUserId();
    Doctrine_Query::create()
      ->update('OnlineIdentity oi')
      ->set('oi.use_as_avatar', 0)
      ->where('oi.user_id = ?', $user_id)
      ->execute();
    $this->setUseAsAvatar(true);
    $this->save();
  }
}