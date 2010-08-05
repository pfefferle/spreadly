<?php

require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('platform', 'prod', true);

sfContext::createInstance($configuration);

$logger = sfContext::getInstance()->getLogger();

// Initialize database manager.
$dbManager = new sfDatabaseManager($configuration);
$dbManager->loadConfiguration();


/**
 *  we need all user id's first
 **/
$lQuery = Doctrine_Query::create()->from('User u')->select('u.id');

$lIds = $lQuery->fetchArray();
foreach ($lIds as $key => $value) {
  $lUserId = $value['id'];
  $lUiCons = UserIdentityConTable::getOnlineIdentityIdsForUser($lUserId);
  UserRelationTable::updateOwnedIdentities($lUserId, $lUiCons);
  foreach ($lUiCons as $lOiId) {
    $lUsersConnected = array();
    $lOiIds = OnlineIdentityConTable::getIdentitysConnectedToOi($lOiId);
    foreach ($lOiIds as $lOi) {
      $lOinlinIdentity = OnlineIdentityTable::getInstance()->find($lOi);
      $lUsersConnected[] = UserIdentityConTable::getUserIdsConnectedToOnlineIdentity($lOinlinIdentity);
    }
    UserRelationTable::updateContactIdentities($lUserId, $lOiIds, $lUsersConnected);

  }

destruct($lOiIds);
destruct($lOinlinIdentity);
destruct($lUiCons);
destruct($lUsersConnected);
  $lUiCons = null;
  $lOiIds = null;
  $lUsersConnected = null;
  echo "user ".$lUserId. "done";
  echo "######### \r\n\r\n";
}

