<?php
/**
 * User
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    yiid
 * @subpackage model
 * @author     Matthias Pfefferle
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class User extends BaseUser {

  /**
   * (non-PHPdoc)
   * @see lib/vendor/symfony/lib/plugins/sfDoctrinePlugin/lib/record/sfDoctrineRecord::__toString()
   */
  public function __toString() {
    return $this->getFullName();
  }

  /**
   * function to add a new identity and connects it with the user model
   *
   * @param string $pIdentity
   * @param string $pService
   * @param integer $pType for example OnlineIdentityPeer::TYPE_URL or OnlineIdentityPeer::TYPE_ACCOUNT
   * @param boolean $pVerified if it is a verified online identity
   * @throws ModelException
   * @return OnlineIdentity $lOnlineIdentity
   */
  public function addOnlineIdentity($pIdentifier, $pCommunityId = null, $pType = OnlineIdentityTable::TYPE_IDENTITY, $pVerified = false, $pAuthIdentifier = null) {
    $lOnlineIdentity = OnlineIdentityTable::addOnlineIdentity($pIdentifier, $pCommunityId, $pType);

    if ($pVerified === false) {
      $pVerified = SocialGraphApi::verifyRelMe($lOnlineIdentity->getUrl(), $this->getYiid(true));
    }

    //UserIdentityConTable::addUserIdentityCon($lOnlineIdentity, $this, $pVerified, $pAuthIdentifier);

    $lOnlineIdentity->setAuthIdentifier($pAuthIdentifier);
    $lOnlineIdentity->save();

    return $lOnlineIdentity;
  }

  /**
   * function to get the full name (first + last-name)
   *
   * @author Matthias Pfefferle
   * @return string fullname or username
   */
  public function getFullName() {
    if ($this->getFirstname() && $this->getLastname()) {
      return $this->getFirstname() . " " . $this->getLastname();
    } else {
      return $this->getUsername();
    }
  }

  /**
   * function to verify a password
   *
   * @param string $pPassword
   * @return boolean
   */
  public function verifyPassword( $pPassword  ) {
    $lHash = PasswordUtils::salt_password( md5($pPassword), $this->getSalt() );
    if( $lHash === $this->getPasswordhash() ){
      return true;
    }
    else{
      return false;
    }
  }

  /**
   * runs before User->save()
   *
   * @author Matthias Pfefferle
   * @param Doctrine_Event $pEvent
   */
  public function preSave($pEvent) {
    $this->setSortname($this->generateSortname());
  }

  /**
   * runs after User->insert()
   *
   * @author Matthias Pfefferle
   * @param Doctrine_Event $pEvent
   */
  public function postInsert($pEvent) {
    // @todo add yiid online identity

    // initialize user's relation object stored in MongoDB
    $lRelation = new UserRelation();
    $lRelation->setUserId($this->getId());
    $lRelation->save();
  }

  /**
   * constructs the sortname of the current user-object
   *
   * @author Matthias Pfefferle
   * @return string
   */
  public function generateSortname() {
    $lSortname = $this->getLastname().$this->getFirstname().$this->getUsername();
    $lSortname = trim($lSortname);

    if (preg_match('/^[^a-zA-Z]/',$lSortname)) {
      $lSortname = '#'.$lSortname;
    }
    return $lSortname;
  }


  public function getOnlineIdentitesAsArray() {
    $lIdentityArray = array();
    $lIdentities = $this->getOnlineIdentities();
    foreach ($lIdentities as $lIdentity) {
      $lIdentityArray[] = $lIdentity->getId();
    }
    return $lIdentityArray;
  }

  public function getOnlineIdentities() {
    return UserIdentityConTable::getOnlineIdentitiesForUser($this->getId());
  }


  public function getTokensForPublishing() {
    return UserTable::getTokensForPublishingByUserId($this->getId());
  }


  /**
   *
   * retrieves main Avatar for User
   * @author weyandch
   * @return UserAvatar
   */
  public function getMainAvatar() {
    $lAvatar = UserAvatarTable::getMainAvatarForUserId($this->getId());
    if (!$lAvatar) {
      return 'affe';
    }
    return $lAvatar->getAvatar();
  }


  /**
   * retrieves a users relation object from MongoDB
   */
  public function retrieveUserRelations() {
    return UserRelationTable::retrieveUserRelations($this->getId());
  }

  /**
   * adds given OnlineIdentities to a users relation table
   *
   * @param array(OnlineIdentity) $pIdentities
   */
  public function updateOwnedIdentities($pIdentities) {
    return UserRelationTable::updateOwnedIdentities($this->getId(), $pIdentities);
  }


  /**
   * add OI's of a user's contacts
   */
  public function updateContacts($pContactUserIds, $pIdentityIds) {
    return UserRelationTable::updateContactIdentities($this->getId(), $pIdentityIds, $pContactUserIds);
  }

  /**
   * counts friends of user
   */
  public function countFriends() {
  	$lFriendIds = UserRelationTable::retrieveUserRelations($this->getId())->getContactUid();
  	return count($lFriendIds);
  }

  /**
   * returns the users yiid
   *
   * @param boolean $pShowHttp
   * @return string
   */
  public function getYiid($pShowHttp = false) {
    if ($pShowHttp) {
      $lYiid = str_replace('%user%', strtolower($this->getUsername()), sfConfig::get('app_settings_yiid'))."/";
    } else {
      $lYiid = str_replace('http://%user%', strtolower($this->getUsername()), sfConfig::get('app_settings_yiid'));
    }
    return $lYiid;
  }

  /**
   * checks if the user has online-identities
   *
   * @author Matthias Pfefferle
   * @return boolean
   */
  public function hasVerifiedOnlineIdentities() {
    $lCount = Doctrine_Query::create()
      ->from('OnlineIdentity oi')
      ->where('oi.user_id = ?', $this->getId())
      ->count();

    if ($lCount > 0) {
      return true;
    } else {
      return false;
    }
  }
}