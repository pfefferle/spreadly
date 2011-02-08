<?php

/**
 * YiidActivity
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    yiid
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class YiidActivity extends BaseYiidActivity {
  public function setTitle($title) {
    $title = StringUtils::cleanupString($title);
    $this->_set("title", $title);
  }

  public function setDescr($desc) {
    $desc = StringUtils::cleanupString($desc);
    $this->_set("descr", $desc);
  }

  public function setSoId($soId)      {
    $soId = new MongoId($soId."");
    $this->_set("so_id", $soId);
  }

  public function setUrl($url)       {
    $url = UrlUtils::cleanupHostAndUri($url);
    $this->_set("url", $url);
    $this->_set("url_hash", md5(UrlUtils::skipTrailingSlash($url)));
  }

  public function setUId($userId)       {
    $this->_set("u_id", intval($userId));
  }

  /**
   * Enter description here...
   * @todo think about verification
   * @param unknown_type $oIIds
   */
  public function setOiids($oIIds)     {
    if (!is_array($oIIds)) {
      $oIIds = explode(',', urldecode($oIIds));
    }

    $this->_set("oiids", $oIIds);
  }

  public function setScore($score)     {
    $this->_set("score", intval($score));
  }

  public function setClickback($clickback) {
    if ($clickback) {
      $lClickback = explode('.', urldecode($clickback));
      if (count($lClickback) > 1) {
        $this->setCbService($lClickback[0]);
        $this->setCbReferer($lClickback[1]);
      }
    }
  }

  public function setTags($tags)      {
    if ($tags && !is_array($tags)) {
      $tags = $this->normalizeTags($tags);
    }

    if ($tags) {
      $this->_set("tags", $tags);
    }
  }

  public function preSave($event) {
    $this->updateDealInfo();
    $this->upsertSocialObject();
    $this->setC(time());
    $this->isAllowedToLike();

    $this->verifyAndSaveOnlineIdentities();
  }

  /**
   * saves are handled by the related Table-Class as we save them in MongoDB
   * (non-PHPdoc)
   * @see lib/vendor/symfony/lib/plugins/sfDoctrinePlugin/lib/vendor/doctrine/Doctrine/Doctrine_Record::save()
   */
  public function save(Doctrine_Connection $conn = null) {
    $this->invokeSaveHooks('pre', 'save');

    $lObjectToSave = $this->toArray(false);

    unset($lObjectToSave['id']);
    unset($lObjectToSave['clickback']);

    if ($this->getId()) {
      $lObjectToSave = YiidActivityTable::updateObjectInMongoDb(array('_id' => new MongoId($this->getId())), $lObjectToSave);
      return false;
    } else {
      $lObjectToSave = YiidActivityTable::saveObjectToMongoDb($lObjectToSave);
      $this->setId($lObjectToSave['_id'].""); // cast mongoID to string
    }
    if ($lObjectToSave) {
      return $lObjectToSave;
    }

    $this->invokeSaveHooks('post', 'save');

    return false;
  }

  public function postSave($event) {
    /* raus schicken
    if (sfConfig::get('sf_environment') != 'dev') {
      // send messages to all services
      foreach ($lOnlineIdentitys as $lIdentity) {
        $lQueryChar = parse_url($pUrl, PHP_URL_QUERY) ? '&' : '?';
        $pPostUrl = $pUrl.$lQueryChar.'yiidit='.$lIdentity->getCommunity()->getCommunity().'.'.$lActivity->getId();
        $lStatus = $lIdentity->sendStatusMessage($pPostUrl, $pVerb, $pScore, $pTitle, $pDescription, $pPhoto);
      }
    }*/

    UserTable::updateLatestActivityForUser($this->getUId(), time());
    $this->updateSocialObjectInfo();

    StatsFeeder::feed($this);
  }

  private function verifyAndSaveOnlineIdentities() {
    $lVerifiedOIs = OnlineIdentityTable::retrieveVerified($this->getUId(), $this->getOiids());

    if (!$lVerifiedOIs) {
      throw new sfException("no valid online identities available", 0);
    }

    foreach ($lVerifiedOIs as $lOI) {
      $lOIIds[] = $lOI->getId();
      $lCIds[] = $lOI->getCommunityId();
    }

    $this->setOiids($lOIIds);
    $this->setCids($lCIds);
  }

  private function updateDealInfo() {
    $deal = DealTable::getActiveDealByHostAndUserId($this->getUrl(), $this->getUId());
    // sets the deal-id if it's not empty
    if ($deal && $deal->isActive()) {
      if ($coupon = $deal->popCoupon()) {
        $this->setDId(intval($deal->getId()));
        $this->setCCode($coupon);
      }
    }
  }

  private function updateSocialObjectInfo() {
    $lSocialObject = SocialObjectTable::retrieveByPk($this->getSoId());
    $lSocialObject->updateObjectOnLikeActivity($this);
  }

  private function upsertSocialObject() {
    $so = SocialObjectTable::retrieveOrCreate($this->getUrl(), $this->getTitle(), $this->getDescr(), $this->getThumb());
    $this->setSoId($so->getId());
  }

  private function isAllowedToLike() {
    $activity = YiidActivityTable::retrieveActionOnObjectById($this->getSoId(), $this->getUId(), $this->getDeal());

    if ($activity) {
      throw new sfException("user has already liked this url", 0);
    }
  }

  /**
   * wrapper for checkeing on clickback
   * @return boolean
   */
  public function isClickback() {
    return $this->getCbReferer()?true:false;
  }

  /**
   * wrapper for checkeing on deal-activity
   * @return boolean
   */
  public function isDealActivity() {
    return $this->getDId()?true:false;
  }

  public function delete(Doctrine_Connection $con = null) {
    $this->collection->remove(array("_id" => new MongoId($this->getId()) ));
  }

  /**
   * at the moment this method returns the name of the first community
   *
   * @author Christian Schätzle
   */
  public function getCommunityNames() {
    $lObjectOiIds = $this->getCids();
    $lNamesArray = array();

    /*
    foreach($lObjectOiIds as $lId) {
      $lOi = Doctrine::getTable('OnlineIdentity')->find($lId);
      $lName = Doctrine::getTable('Community')->find($lOi->getCommunityId())->getName();
      array_push($lNamesArray, $lName);
    }

    return $lNamesArray;
    */

    return Doctrine::getTable('Community')->find($lObjectOiIds[0])->getName();
  }

  /**
   * This method returns the minuts/hours/day since publishing of this activity
   *
   * @author Christian Schätzle
   */
  public function getPublishingTime() {
    $lSocialObjectDate = $this->getC();

    sfProjectConfiguration::getActive()->loadHelpers(array('Date'));
    $lDate = distance_of_time_in_words($lSocialObjectDate);

    return $lDate;
  }

  public function getUser() {
    $lUser = UserTable::getInstance()->retrieveByPk($this->getUId());

    return $lUser;
  }

  /**
   * checks if it is a deal activity or not
   *
   * @return boolean
   */
  public function isDeal() {
    if ($this->getDId()) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Returns the associated Deal
   *
   * @return Deal|null
   */
  public function getDeal() {
    if ($this->getDId()) {
      return DealTable::getInstance()->find($this->getDId());
    } else {
      return null;
    }
  }

  /**
   * normalize the tag-string
   *
   * @param string $tags
   * @return array
   */
  private function normalizeTags($pTags) {
    $lTags = urldecode($pTags);
    $lTags = explode(",", $lTags);

    if (is_array($lTags)) {
      $lTagArray = array();
      foreach ($lTags as $key => $value) {
        $lTagArray[$key] = trim($value);
      }

      $lTagArray = array_unique($lTagArray);

      return $lTagArray;
    } else {
      return null;
    }
  }

  /**
   * Returns the associated SocialObject
   *
   * @return SocialObject|null
   */
  public function getSocialObject() {
    if ($this->getSoId()) {
      return SocialObjectTable::getInstance()->retrieveByPK($this->getSoId());
    } else {
      return null;
    }
  }
}
