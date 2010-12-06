<?php
require_once('../../../lib/utils/UrlUtils.php');
require_once('../../../lib/vendor/sqs.php');
require_once('../../../lib/utils/YiidStatsSingleton.php');

/**
 *
 *
 * @author Christian Weyand
 */
class MongoSessionPeer {

  const MONGO_COLLECTION = 'session';

  /**
   * extracts the userid from the current session
   *
   * @author Christian Weyand
   * @param $pSessionId
   * @return int|boolean
   */
  public static function extractUserIdFromSession($pSessionId) {
    $lSession = self::getSessionData($_COOKIE[$pSessionId]);

    $lEncodedData = $lSession['sess_data'];
    session_decode($lEncodedData);
    $pUserId = $_SESSION['symfony/user/sfUser/attributes']['user_session']['id'];
    return $pUserId?$pUserId:false;
  }

  /**
   * returns the complete session-data
   *
   * @author Christian Weyand
   * @param $pId
   * @return unknown_type
   */
  public static function getSessionData($pId) {
    $lMongo = new Mongo(LikeSettings::MONGO_HOSTNAME);
    $pCollectionObject = $lMongo->selectCollection(LikeSettings::MONGO_DATABASENAME, self::MONGO_COLLECTION);

    return $pCollectionObject->findOne(array("sess_id" => $pId) );
  }
}

/**
 * Wrapper for accessing MongoObject without Sf Context (raw button)
 *
 * @author Christian Weyand
 */
class SocialObjectPeer {

  const MONGO_COLLECTION = 'social_object';

  /**
   * returns socialobject for a given url
   *
   * @param string $pUrl
   * @param int $pIsUsed
   * @return array()
   */
  public static function getDataForUrl($pUrl, $pIsUsed = false) {
    $pUrl = str_replace(" ", "+", $pUrl);

    $lMongo = new Mongo(LikeSettings::MONGO_HOSTNAME);
    $pCollectionObject = $lMongo->selectCollection(LikeSettings::MONGO_DATABASENAME, self::MONGO_COLLECTION);
    $pUrlHash = md5(UrlUtils::skipTrailingSlash($pUrl));

    // check if we know the URL already
    $lSocialObjectArray = $pCollectionObject->findOne(array("alias" => array('$in' => array($pUrlHash)) ));

    // if no data is available, initialize empty array & create social object
    if (!$lSocialObjectArray) {
      // don't do that.. too much objects in the queue atm
      //self::delegateSocialObjectParsing($pUrl);
      $lSocialObjectArray = array();
    }

    if($pUrl && !UrlUtils::isUrlValid($pUrl)) {
      $lSocialObjectArray = array_merge(array(
	      'urlerror'   => true,
      ), $lSocialObjectArray);
    }

    // set counts for like on 0 if they're not set yet
    $lSocialObjectArray = array_merge(array(
      'l_cnt'   => 0,
      'd_cnt' => 0,
    ), $lSocialObjectArray);

    return $lSocialObjectArray;
  }

  /**
   * If an user did vote on an social object withdraw his score from displayed result
   *
   * @param array() $pSocialObjectArray
   * @param boolean $pUserAction
   * @return array()
   */
  public static function recalculateCountsRespectingUser($pSocialObjectArray, $pUserAction = false) {

    if ($pUserAction !== false) {
      switch ($pUserAction) {
        case 1: $pSocialObjectArray['l_cnt'] = $pSocialObjectArray['l_cnt']-1;
        break;
        case -1: $pSocialObjectArray['d_cnt'] = $pSocialObjectArray['d_cnt']-1;
        break;
      }
    }
    return $pSocialObjectArray;
  }


  /**
   * prototype function to send msgs in a queue
   * push a note to amazon sqs.. alpha stage!
   *
   * @author Christian Weyand
   */
  public static function delegateSocialObjectParsing($pUrl) {
    if ($pUrl) {
      $queue = 'SocialObjectParser'.'-'.LikeSettings::ENVIRONMENT;

      $service = new SQS('AKIAJ5NSA6ET5RC4AMXQ','bs1YgS4c1zJN/HmwaVA8CkhNfyvcS+EEm1hcEOa0');
      $service->createQueue($queue);

      $pUrl = urlencode($pUrl);
      $service->sendMessage($queue, $pUrl);
    }
  }
}

/**
 * Wrapper to access activity items within th ebutton
 */
class YiidActivityObjectPeer {
  const MONGO_COLLECTION = 'yiid_activity';


  /**
   * check if a given user already performed an action on a social object
   * returns false if not, or it's score (1/-1)
   *
   * @author Christian Weyand
   * @param int $pSocialObjectId
   * @param int $pUserId
   * @return false or score of action taken (-1/1)
   */
  public static function actionOnObjectByUser($pSocialObjectId, $pUserId, $pActiveDeal = null) {
    $lMongo = new Mongo(LikeSettings::MONGO_HOSTNAME);
    $pCollectionObject = $lMongo->selectCollection(LikeSettings::MONGO_DATABASENAME, 'yiid_activity');

    // get yiid-activity and factor a deal
    if ($pActiveDeal) {
      $lObject = $pCollectionObject->findOne(array("so_id" => $pSocialObjectId,
                                                   "u_id" => intval($pUserId),
                                                   "d_id" => intval($pActiveDeal['id'])
                                                  ));

    } else {
      $lObject = $pCollectionObject->findOne(array("so_id" => $pSocialObjectId,
                                                   "u_id" => intval($pUserId),
                                                   "d_id" => array('$exists' => false)
                                                  ));
    }
    //return $lObject?$lObject['score']:false;

    return $lObject;
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
  public static function actionOnHostByUser($pUserId, $pActiveDeal) {
    $lMongo = new Mongo(LikeSettings::MONGO_HOSTNAME);
    $pCollectionObject = $lMongo->selectCollection(LikeSettings::MONGO_DATABASENAME, 'yiid_activity');

    // get yiid-activity and factor a deal
    $lObject = $pCollectionObject->findOne(array("u_id" => intval($pUserId),
                                                 "d_id" => intval($pActiveDeal['id'])
                                                  ));
    return $lObject;
  }
}

class StatsHelper {
  public static function trackPageImpression($pUrl, $pClickback, $pUser) {
    $lHost = parse_url($pUrl, PHP_URL_HOST);

    $lMongo = new Mongo(LikeSettings::MONGO_HOSTNAME);
    $lCollection = $lMongo->selectCollection(LikeSettings::MONGO_STATS_DATABASENAME, str_replace('.', '_', $lHost).".analytics.pis");

    if (mb_detect_encoding($pUrl) != 'UTF-8') {
      $pUrl = utf8_encode($pUrl);
    }

    $lDoc = array(
      'url' => $pUrl,
      'date' => new MongoDate(strtotime(date("Y-m-d")))
    );

    $lOptions = array("total" => 1);

    if ($pUser) {
      $lOptions["yiid"] = 1;
    }

    if ($pClickback) {
      $lOptions["cb"] = 1;
    }

    $lCollection->update($lDoc, array('$inc' => $lOptions), array("upsert" => true));
  }
}

class ClickBackHelper {

  /**
   * extracts yiidit Parameter if it's given, returns null if its not set or referal == buttonUro
   *
   * @param string $pButtonUri
   * @param string $pReferrerUri
   * @return string|null
   */
  public static function extractClickback($pButtonUri, $pReferrerUri = null) {
    if (!$pReferrerUri) {
      return null;
    }
    if ($pButtonUri == $pReferrerUri) {
      return null;
    }

    $lParameterList = parse_url($pReferrerUri);

    $lGetParams = array();
    parse_str($lParameterList['query'], $lGetParams);

    return (isset($lGetParams['yiidit']))?$lGetParams['yiidit']:null;
  }
}

/**
 *
 *
 * @author hannes
 */
class DealUtils {
  const MONGO_COLLECTION = 'deals';

  public static function dealActive($pUrl) {
    $pUrl = str_replace(" ", "+", $pUrl);
    $pUrl = UrlUtils::skipTrailingSlash($pUrl);

    $host = parse_url($pUrl, PHP_URL_HOST);
    $col = DealUtils::getCollection();
    $today = new MongoDate(strtotime("today"));
    $cond = array(
      "host" => $host,
      "start_date" => array('$lte' => $today),
      "end_date" => array('$gte' => $today)
    );
    $result = $col->find($cond)->limit(1)->sort(array("start_date" => -1));

    $deal = $result->getNext();

    if ($deal && ($deal["is_unlimited"] == true || $deal['remaining_coupon_quantity'] > 0)) {
      return $deal;
    }

    return false;
  }

  public static function checkHostActive($pUrl, $pUserId, $pDealId) {
    $pUrl = str_replace(" ", "+", $pUrl);
    $pUrl = UrlUtils::skipTrailingSlash($pUrl);

    $host = parse_url($pUrl, PHP_URL_HOST);

    $lMongo = new Mongo(LikeSettings::MONGO_HOSTNAME);
    $pCollectionObject = $lMongo->selectCollection(LikeSettings::MONGO_DATABASENAME, YiidActivityObjectPeer::MONGO_COLLECTION);

    $cond = array(
      "host" => $host,
      "start_date" => array('$lte' => $today),
      "end_date" => array('$gte' => $today)
    );
    $result = $col->find($cond)->limit(1)->sort(array("start_date" => -1));

    $deal = $result->getNext();

    if ($deal && ($deal["is_unlimited"] == true || $deal['remaining_coupon_quantity'] > 0)) {
      // check if activity already liked

      // check if domain already liked

      return $deal;
    }

    return false;
  }

  private static function getCollection() {
    $lMongo = new Mongo(LikeSettings::MONGO_HOSTNAME);
    return $lMongo->selectCollection(LikeSettings::MONGO_DATABASENAME, self::MONGO_COLLECTION);
  }
}