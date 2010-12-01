<?php


class YiidActivityTable extends Doctrine_Table
{
  const ACTIVITY_VOTE_POSITIVE = 1;
  const ACTIVITY_VOTE_NEGATIVE = -1;

  const MONGO_COLLECTION_NAME = 'yiid_activity';

  public static $aTypes = array("like","pro","recommend","visit","nice","buy","rsvp");

  public static function getInstance() {
    return Doctrine_Core::getTable('YiidActivity');
  }

  public static function getMongoCollection() {
    return MongoDbConnector::getInstance()->getCollection(sfConfig::get('app_mongodb_database_name'), self::MONGO_COLLECTION_NAME);
  }

  /**
   * Save a given object to our MongoDb
   * @param unknown_type $lObject
   */
  public static function saveObjectToMongoDb($lObject) {
    $lCollection = self::getMongoCollection();
    $lObject = array_filter($lObject);
    $lCollection->save($lObject);
    return $lObject;
  }

  /**
   * Updates an object in Mongo, respecting MongoDB's Syntax to manipulate stored objects
   *
   * @see  http://www.mongodb.org/display/DOCS/Updating
   *
   * @param Array() $pIdentifier
   * @param Array() $pManipualtior
   */
  public static function updateObjectInMongoDb($pIdentifier, $pManipulator) {
    $lCollection = self::getMongoCollection();
    $lCollection->update($pIdentifier, $pManipulator, array('upsert' => true));
  }

  public static function saveLikeActivitys($pUserId,
                                           $pUrl,
                                           $pOwnedOnlineIdentitys = array(),
                                           $pGivenOnlineIdentitys = array(),
                                           $pScore = self::ACTIVITY_VOTE_POSITIVE,
                                           $pVerb = 'like',
                                           $pTitle = null,
                                           $pDescription = null,
                                           $pPhoto = null,
                                           $pClickback = null,
                                           $pCouponCode = null,
                                           $pDeal = null
                                          ) {

    $lSuccess = false;
    $lVerifiedOnlineIdentitys = array();
    $pClickback = urldecode($pClickback);
    $pTitle = StringUtils::cleanupStringForMongodb($pTitle);
    $pDescription = StringUtils::cleanupStringForMongodb($pDescription);

    // array of services we're sharing to
    $lServices = array();

    if (!self::isVerbSupported($pVerb)) {
      return false;
    }

    $pUrl = UrlUtils::cleanupHostAndUri($pUrl);
    $lSocialObject = self::retrieveSocialObjectByAliasUrl($pUrl);


    // object doesn't exist, create it now
    if (!$lSocialObject) {
      $lSuccess = SocialObjectTable::initializeObjectFromUrl($pUrl);
      if ($lSuccess === false) {
        return  false;
      }
      $lSocialObject = self::retrieveSocialObjectByAliasUrl($pUrl);
      $lSocialObject->updateObjectMasterData($pTitle, $pDescription, $pPhoto);
    } // object exists, we need to check if user is allowed to make an action on it
    elseif (!self::isActionOnObjectAllowed($lSocialObject->getId(), $pUserId)) {
      return false;
    }

    // check if all onlineidentity-ids are valid
    foreach ($pGivenOnlineIdentitys as $lIdentityId) {
      if (in_array($lIdentityId, $pOwnedOnlineIdentitys)) {
        $lVerifiedOnlineIdentityIds[]= $lIdentityId;
        $senderOi = OnlineIdentityTable::getInstance()->retrieveByPk($lIdentityId);
        $lOnlineIdentitys[] = $senderOi;
        $lServices[] = $senderOi->getCommunityId();
      }
    }

    // save yiid activity
    $lActivity = self::saveActivity($lSocialObject, $pUrl, $pUserId, $lVerifiedOnlineIdentityIds, $lServices, $pScore, $pVerb, $pClickback);
    if (sfConfig::get('app_settings_environment') != 'dev') {
      // send messages to all services
      foreach ($lOnlineIdentitys as $lIdentity) {
        $parameterList = parse_url($pUrl);
        if (isset($parameterList['query'])) {
          $pPostUrl = $pUrl.'&yiidit='.$lIdentity->getCommunity()->getCommunity().'.'.$lActivity->getId();
        }
        else {
          $pPostUrl = $pUrl.'?yiidit='.$lIdentity->getCommunity()->getCommunity().'.'.$lActivity->getId();
        }
        $lStatus = $lIdentity->sendStatusMessage($pPostUrl, $pVerb, $pScore, $pTitle, $pDescription, $pPhoto);
      }
    }

    sfContext::getInstance()->getLogger()->debug("{YiidActivityPeer}{saveLikeActivitys} Status Message: " . print_r($lVerifiedOnlineIdentityIds, true));

    if (!empty($lVerifiedOnlineIdentityIds)) {
      $lSocialObject = self::retrieveSocialObjectByAliasUrl($pUrl);
      $lSocialObject->updateObjectOnLikeActivity($pUserId, $lVerifiedOnlineIdentityIds, $pUrl, $pScore, $lServices);
      UserTable::updateLatestActivityForUser($pUserId, time());
    }

    // notification
    $lUser = UserTable::getInstance()->retrieveByPk($pUserId);
    StatsFeeder::feed($lActivity, $lUser, $lSocialObject);

    return true;
  }

  public static function storeTemporary($pSessionId,
                                        $pUrl,
                                        $pOwnedOnlineIdentitys = array(),
                                        $pGivenOnlineIdentitys = array(),
                                        $pScore = self::ACTIVITY_VOTE_POSITIVE,
                                        $pVerb = 'like',
                                        $pTitle = null,
                                        $pDescription = null,
                                        $pPhoto = null,
                                        $pClickback = null
                                       ) {
    $lStorageArray = array();
    $lStorageArray['url'] = $pUrl;
    $lStorageArray['score'] = $pScore;
    $lStorageArray['verb'] = $pVerb;
    $lStorageArray['clickback'] = $pClickback;
    $lStorageArray['title'] = $pTitle;
    $lStorageArray['description'] = $pDescription;
    $lStorageArray['photo'] = $pPhoto;

    $lPersist = new PersistentVariable();
    $lPersist->setName('widgetauth_'.$pSessionId);
    $lPersist->setValue(serialize($lStorageArray));
    $lPersist->save();
    return false;
  }

  /**
   * retrieve data saved with this sess_id
   *
   * @author weyandch
   * @param $pSessionId
   * @return unknown_type
   */
  public static function getTemporaryData($pSessionId) {
    $lPersist = PersistentVariableTable::retrieveByName('widgetauth_'.$pSessionId);
    if ($lPersist) {
      $lReturn = unserialize($lPersist->getValue());
      $lPersist->delete();
      return $lReturn;
    } else {
      return null;
    }
  }

  /**
   * generates a new yiid-activity
   *
   * @author Christian Weyand
   * @param $pSocialObjectId
   * @param $pOnlineIdentity
   * @param $pType
   * @return YiidActivity
   */
  public static function saveActivity($pSocialObject,
                                      $pUrl,
                                      $pUserId,
                                      $pOnlineIdentitys,
                                      $pServicesId,
                                      $pScore,
                                      $pVerb,
                                      $pClickback = null,
                                      $pCouponCode = null,
                                      $pDeal = null
                                     ) {
    $lActivity = new YiidActivity();
    $lActivity->setUId($pUserId);
    $lActivity->setSoId($pSocialObject->getId());
    $lActivity->setUrl($pUrl);
    $lActivity->setUrlHash(md5(UrlUtils::skipTrailingSlash($pUrl)));
    $lActivity->setOiids($pOnlineIdentitys);
    $lActivity->setCids($pServicesId);
    $lActivity->setScore($pScore);
    $lActivity->setVerb($pVerb);
    $lActivity->setC(time());

    // set clickback if exists
    if ($pClickback) {
      $lClickback = explode('.', $pClickback);
      if (count($lClickback) > 1) {
        $lActivity->setCbService($lClickback[0]);
        $lActivity->setCbReferer($lClickback[1]);
      }
    }
    $lActivity->save();

    return $lActivity;
  }

  /**
   *
   * @author Christian Weyand
   * @param $pSocialObjectId
   * @param $pOnlineIdentitys
   * @return boolean
   */
  public static function isActionOnObjectAllowed($pSocialObjectId, $pUserId) {
    $lAlreadyPerformedActivity = self::retrieveActionOnObjectById($pSocialObjectId, $pUserId);
    return $lAlreadyPerformedActivity?false:true;
  }

  /**
   * check if a given user already performed an action on a social object
   * returns false if not, or it's score (1/-1)
   *
   * @author Christian Weyand
   * @param int $pSocialObjectId
   * @param int $pUserId
   * @return false or score of action taken (-1/1)
   */
  public static function getActionOnObjectByUser($pSocialObjectId, $pUserId) {
    $lAction = self::retrieveActionOnObjectById($pSocialObjectId, $pUserId);
    return $lAction?$lAction->getScore():false;
  }

  /**
   *
   * @author Christian Weyand
   * @param $pSocialObjectId
   * @param $pOnlineIdentitys
   * @return unknown_type
   */
  public static function retrieveActionOnObjectById($pSocialObjectId, $pUserId) {
    $lCollection = self::getMongoCollection();
    return self::initializeObjectFromCollection($lCollection->findOne(array("so_id" => new MongoId($pSocialObjectId.""), "u_id" => (int)$pUserId ) ));
  }

  public static function retrieveLatestActivitiesByContacts($pUserId, $pFriendId = null, $pCommunityId = null, $pRangeDays = 30, $pOffset = 10, $pLimit = 1) {
    $lCollection = self::getMongoCollection();

    $lRelevantOis = OnlineIdentityTable::getRelevantOnlineIdentityIdsForQuery($pUserId, $pFriendId);

    // we don't have contacts, so we MUST NOT use an $in Query on mongodb
    //   if (empty($lRelevantOis)) {
    //    return array();
    //   }
    $lQueryArray = array();
    $lQueryArray['oiids'] = array('$in' => $lRelevantOis);
    if ($pCommunityId) {
      $lQueryArray['cids'] = array('$in' => array($pCommunityId));
    }
    if ($pRangeDays > 0) {
      $lQueryArray['u'] = array('$gte' => strtotime('-'.$pRangeDays. ' days'));
    }

    $lResults = $lCollection->find($lQueryArray);
    $lResults->sort(array('c' => -1));
    $lResults->limit($pLimit)->skip(($pOffset - 1) * $pLimit);
    return self::hydrateMongoCollectionToObjects($lResults);
  }


  /**
   * retrieve YiidActivies for a given SovialObject MongoDbID
   *
   * @author Christian Weyand
   * @param $pId
   * @param integer $pLimit
   * @param integer $pPage
   * @return unknown_type
   */
  public static function retrieveByYiidActivityId($pUserId, $pId, $pCase, $pLimit = 10, $pOffset = 1){
    $lCollection = self::getMongoCollection();
    $lRelevantOis = OnlineIdentityTable::getRelevantOnlineIdentityIdsForQuery($pUserId, null);
    $lQueryArray = array();
    // filters friends
    //$lQueryArray['oiids'] = array('$in' => $lRelevantOis);
    $lQueryArray['so_id'] = new MongoId($pId);
    $lQueryArray = array_merge($lQueryArray, self::addCaseQuery($pCase));

    $lResults = $lCollection->find($lQueryArray);
    $lResults->sort(array('c' => -1));

    $lResults->limit($pLimit)->skip(($pOffset - 1) * $pLimit);

    return self::hydrateMongoCollectionToObjects($lResults);
  }



  /**
   * transforms $pCase from action into score values for MongoDB
   * @param string $pCase
   * @author weyandch
   * @return int
   */
  public static function addCaseQuery($pCase) {
    if ($pCase == 'hot') {
      return array('score' => 1);
    } elseif ($pCase == 'not') {
      return array('score' => -1);
    }
    return array();
  }


  /**
   * check if the given verb is supported in the current version
   *
   * @param $pVerb
   * @return boolean
   */
  public static function isVerbSupported($pVerb) {
    return in_array($pVerb, sfConfig::get('app_likewidget_types'));
  }

  /**
   * @author weyandch
   * @param $pUrl
   * @return unknown_type
   */
  public static function retrieveSocialObjectByUrl($pUrl) {
    return SocialObjectTable::retrieveByAliasUrl($pUrl);
  }

  /**
   * retrieves an social object for a given url
   *
   * @author Christian Weyand
   * @param string $pUrl
   * @return SocialObject
   */
  public static function retrieveSocialObjectByAliasUrl($pUrl) {
    return SocialObjectTable::retrieveByAliasUrl($pUrl);
  }

  /**
   *
   * @author Christian Weyand
   * @param $pCollection
   * @return unknown_type
   */
  public static function initializeObjectFromCollection($pCollection) {
    $lObject = new YiidActivity();
    if ($pCollection) {
      $lObject->fromArray($pCollection);
      $lObject->setId($pCollection['_id']."");
      return $lObject;
    }
    return null;
  }



  /**
   * hydrate yiidactivities objects from the extracted collection and return an array
   *
   * @param unknown_type $pCollection
   * @return array(SocialObject)
   */
  private static function hydrateMongoCollectionToObjects($pCollection) {
    $lObjects = array();
    while($pCollection->hasNext()) {

      $lObjects[] = self::initializeObjectFromCollection($pCollection->getNext());
    }
    return $lObjects;
  }


  public static function retrieveAllObjects($pLimit = 0, $pOffset = 0) {
    $lCollection = self::getMongoCollection();
    $lMongoCursor = $lCollection->find();
    $lMongoCursor->limit($pLimit)->skip($pOffset);


    return self::hydrateMongoCollectionToObjects($lMongoCursor);
  }

}