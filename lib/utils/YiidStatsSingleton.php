<?php
class YiidStatsSingleton {

  // new schema
  const MONGO_COLLECTION_NAME_VISIT = 'visit';
  const TYPE_LIKE = 'likes';
  const TYPE_DISLIKE = 'dislikes';


  /**
   * tracks a visit on given url, if the $pLikeType variable is passed an activity is tracked too
   *
   * @param string $pUrl
   * @param string $pLikeType see TYPE_* Constants in this class
   * @author weyandch
   */
  public static function trackVisit($pUrl) {
    $lMongo = new Mongo(LikeSettings::MONGO_HOSTNAME);
    $lCollection = $lMongo->selectCollection(LikeSettings::MONGO_STATS_DATABASENAME, self::MONGO_COLLECTION_NAME_VISIT);

    $pUrl = urldecode($pUrl);

    // data is stored as a tupel of host && month (host => example.com, month => 2010-10)
    $lQueryArray = array();
    $lQueryArray['host'] = parse_url($pUrl, PHP_URL_HOST);
    $lQueryArray['month'] = date('Y-m');

    if ($lQueryArray['host'] != '') {
      $lUpdateArray = array( '$inc' => array('stats.day_'.date('d').'.pis' => 1, 'pis_total' => 1));
      $lCollection->update($lQueryArray, $lUpdateArray, array('upsert' => true));
    }
  }



  /**
   *
   * tracks a count for given $pLikeType
   *
   * @param string $pUrl Full Request URI (http://example.com/page)
   * @param string $pLikeType see TYPE_* Constants in this class
   */
  public static function trackClick($pUrl, $pLikeType) {
    $lCollection = MongoDbConnector::getInstance()->getCollection(sfConfig::get('app_mongodb_database_name_stats'), self::MONGO_COLLECTION_NAME_VISIT);
    $pUrl = urldecode($pUrl);

    // data is stored as a tupel of host && month (host => example.com, month => 2010-10)
    $lQueryArray = array();
    $lQueryArray['host'] = parse_url($pUrl, PHP_URL_HOST);
    $lQueryArray['month'] = date('Y-m');

    if ($pLikeType) {
      // increases the likes/dislikes count
      $lUpdateArray = array( '$inc' => array('stats.day_'.date('d').'.'.$pLikeType => 1, $pLikeType.'_total' => 1, 'act_total' => 1));
    }

    $lCollection->update($lQueryArray, $lUpdateArray, array('upsert' => true));
  }


 /**
   * tracks a visit on given url, if the $pLikeType variable is passed an activity is tracked too
   *
   * @param string $pUrl
   * @param string $pLikeType see TYPE_* Constants in this class
   * @deprecated don't use this at home
   * @author weyandch
   */
  public static function trackVisitForMigration($pUrl, $pTime) {
    $lCollection = MongoDbConnector::getInstance()->getCollection(sfConfig::get('app_mongodb_database_name_stats'), self::MONGO_COLLECTION_NAME_VISIT);

//   $pUrl = urldecode($pUrl);

    // data is stored as a tupel of host && month (host => example.com, month => 2010-10)
    $lQueryArray = array();

    $lQueryArray['host'] = $pUrl;
    $lQueryArray['month'] = date('Y-m', $pTime);

    $lUpdateArray = array( '$inc' => array('stats.day_'.date('d', $pTime).'.pis' => 1, 'pis_total' => 1));

    return $lCollection->update($lQueryArray, $lUpdateArray, array('upsert' => true));

  }


 /**
   *
   * tracks a count for given $pLikeType
   *
   * @param string $pUrl Full Request URI (http://example.com/page)
   * @param string $pLikeType see TYPE_* Constants in this class
   * @deprecated don't use this at home
   */
  public static function trackClickForMigration($pUrl, $pLikeType, $pTime) {
    $lCollection = MongoDbConnector::getInstance()->getCollection(sfConfig::get('app_mongodb_database_name_stats'), self::MONGO_COLLECTION_NAME_VISIT);


    // data is stored as a tupel of host && month (host => example.com, month => 2010-10)
    $lQueryArray = array();
    $lQueryArray['host'] = $pUrl;
    $lQueryArray['month'] = date('Y-m', $pTime);

    $pLikeType = $pLikeType==1?'likes':'dislikes';
    if ($pLikeType) {
      // increases the likes/dislikes count
      $lUpdateArray = array( '$inc' => array('stats.day_'.date('d', $pTime).'.'.$pLikeType => 1, $pLikeType.'_total' => 1, 'act_total' => 1));
    }

    $lCollection->update($lQueryArray, $lUpdateArray, array('upsert' => true));
  }

}
?>