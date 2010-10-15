<?php
/**
 * Enter description here...
 *
 * @author Matthias Pfefferle
 */
class TwitterAuthApiClient extends AuthApi {

  protected $aCommunity = "twitter";

  /**
   * generates a OAuthConsumer
   *
   * @author Matthias Pfefferle
   * @return OAuthConsumer
   */
  public function getConsumer() {
    $lConsumer = new OAuthConsumer(sfConfig::get("app_twitter_oauth_token"), sfConfig::get("app_twitter_oauth_secret"));

    return $lConsumer;
  }

  /**
   * start the sign in process
   *
   * @author Matthias Pfefferle
   * @param myUser $pSessionUser
   * @param AuthToken $pAuthToken
   */
  public function doSignin($pSessionUser, $pOAuthToken) {
    $lAccessToken = $this->getAccessToken($pOAuthToken);

    // get params
    $lParams = $lAccessToken->params;
    $lParamsArray = array();
    // extract params
    parse_str($lParams, $lParamsArray);

    // twitter identifier
    $lIdentifier = "http://twitter.com/account/profile?user_id=".$lParamsArray['user_id'];

    // ask for online identity
    $lOnlineIdentity = OnlineIdentityTable::retrieveByAuthIdentifier($lIdentifier);

    // check if user already exists
    if ($lOnlineIdentity) {
      $lUser = $lOnlineIdentity->getUser();
    } else {
      // check online identity
      $lOnlineIdentity = OnlineIdentityTable::addOnlineIdentity("http://twitter.com/".$lParamsArray['screen_name'], $lParamsArray['user_id'], $this->aCommunityId);
      // generate empty user
      $lUser = new User();
    }

    if (!$lUser || !$lUser->getId()) {
      // get api informations
      $lJson = OAuthClient::get($this->getConsumer(), $lParamsArray['oauth_token'], $lParamsArray['oauth_token_secret'], "http://api.twitter.com/1/users/show.json?user_id=".$lParamsArray['user_id']);
      $lJsonObject = json_decode($lJson);

      // use api complete informations
      $this->completeOnlineIdentity($lOnlineIdentity, $lJsonObject);
      $this->completeUser($lUser, $lJsonObject);

      $lOnlineIdentity->setUserId($lUser->getId());
      $lOnlineIdentity->setAuthIdentifier($lIdentifier);
      $lOnlineIdentity->save();

      // delete connected user-cons
      UserIdentityConTable::deleteAllConnections($lOnlineIdentity->getId());

      //TwitterImportClient::importContacts($lUser->getId(), $lOnlineIdentity);
      $lImgPath = "http://api.twitter.com/1/users/profile_image/".$lParamsArray['screen_name']."?size=bigger";
      $pPayload = serialize(array('path' => $lImgPath, 'user_id' => $lUser->getId(), 'oi_id' => $lOnlineIdentity->getId()));
      AmazonSQSUtils::pushToQuque('ImageImport', $pPayload);
     // ImageImporter::importByUrlAndUserId($lImgPath, $lUser->getId(), $lOnlineIdentity);
    }

    // import contacts
    $this->importContacts($lOnlineIdentity->getId());

    AuthTokenTable::saveToken($lUser->getId(), $lOnlineIdentity->getId(), $lParamsArray['oauth_token'], $lParamsArray['oauth_token_secret'], true);

    return $lUser;
  }

  /**
   * add identifier
   *
   * @author Matthias Pfefferle
   * @param User $pUser
   * @param AuthToken $pAuthToken
   * @return OnlineIdentity
   */
  public function addIdentifier($pUser, $pOAuthToken) {
    $lAccessToken = $this->getAccessToken($pOAuthToken);

    // get params
    $lParams = $lAccessToken->params;
    $lParamsArray = array();
    // extract params
    parse_str($lParams, $lParamsArray);

    // twitter identifier
    $lIdentifier = "http://twitter.com/account/profile?user_id=".$lParamsArray['user_id'];

    // ask for online identity
    $lOnlineIdentity = OnlineIdentityTable::retrieveByAuthIdentifier($lIdentifier);

    // check if user already exists
    if ($lOnlineIdentity) {
      if ($lOnlineIdentity->getUserId() && ($pUser->getId() == $lOnlineIdentity->getUserId())) {
        throw new sfException("online identity already added", 1);
      } elseif ($lOnlineIdentity->getUserId() && ($pUser->getId() != $lOnlineIdentity->getUserId())) {
        throw new sfException("online identity already added by someone else", 2);
      }
    } else {
      // check online identity
      $lOnlineIdentity = OnlineIdentityTable::addOnlineIdentity($lParamsArray['screen_name'], $lParamsArray['user_id'], $this->aCommunityId);
    }

    // delete connected user-cons
    UserIdentityConTable::deleteAllConnections($lOnlineIdentity->getId());

    // get api informations
    $lJson = OAuthClient::get($this->getConsumer(), $lParamsArray['oauth_token'], $lParamsArray['oauth_token_secret'], "http://api.twitter.com/1/users/show.json?user_id=".$lParamsArray['user_id']);
    $lJsonObject = json_decode($lJson);

    // use api complete informations
    $this->completeOnlineIdentity($lOnlineIdentity, $lJsonObject);
    $this->completeUser($pUser, $lJsonObject);

    // @todo <todo> encapsulating this
    $lOnlineIdentity->setUserId($pUser->getId());
    $lOnlineIdentity->setAuthIdentifier($lIdentifier);
    $lOnlineIdentity->save();

    $this->importContacts($lOnlineIdentity->getId());

    AuthTokenTable::saveToken($pUser->getId(), $lOnlineIdentity->getId(), $lParamsArray['oauth_token'], $lParamsArray['oauth_token_secret'], true);
    return $lOnlineIdentity;
  }

  /**
   * ask twitter for an access-key
   *
   * @author Matthias Pfefferle
   * @return OAuthToken
   */
  public function getRequestToken() {
    $lRequestToken = OAuthClient::getRequestToken($this->getConsumer(), "https://api.twitter.com/oauth/request_token", 'GET', array("oauth_callback" => $this->getCallbackUri()));

    // save the request token
    OauthRequestTokenTable::saveToken($lRequestToken, $this->getCommunity());

    return $lRequestToken;
  }

  /**
   * do the athentication on twitter and redirect to twitter
   *
   * @author Matthias Pfefferle
   * @link http://dev.twitter.com/pages/sign_in_with_twitter
   */
  public function doAuthentication() {
    $lRequestToken = self::getRequestToken();
    $lRequest = OAuthClient::prepareRequest($this->getConsumer(), $lRequestToken, "GET", "http://api.twitter.com/oauth/authenticate");
    // redirect
    header("Location: " . $lRequest->to_url());
    // do nothing more
    exit;
  }

  /**
   * ask twitter for an access token
   *
   * @author Matthias Pfefferle
   * @param string $pTokenKey
   */
  public function getAccessToken($pOAuthToken) {
    $lAccessToken = OAuthClient::getAccessToken($this->getConsumer(), "http://api.twitter.com/oauth/access_token", $pOAuthToken, "GET", array("oauth_verifier" => $pOAuthToken->verifier));

    return $lAccessToken;
  }

  /**
   * complete the user with the api json
   *
   * @param User $pUser
   * @param Object $pObject
   */
  public function completeUser(&$pUser, $pObject) {
    $pUser->setUsername(UserUtils::getUniqueUsername(StringUtils::normalizeUsername($pObject->screen_name)));
    $pUser->setDescription($pObject->description);
    // try to split full-name
    $lName = MicroformatsTools::splitFN($pObject->name);
    if (array_key_exists("firstname", $lName)) {
      $pUser->setFirstname($lName['firstname']);
    }
    if (array_key_exists("lastname", $lName)) {
      $pUser->setFirstname($lName['lastname']);
    }

    $pUser->setCulture(substr($pObject->lang, 0, 2));
    $pUser->save();
  }

  /**
   * complete the online-identity with the api json
   *
   * @param OnlineIdentity $pOnlineIdentity
   * @param Object $pObject
   */
  public function completeOnlineIdentity(&$pOnlineIdentity, $pObject) {
    if ($pObject->name) {
      $pOnlineIdentity->setName($pObject->name);
    } else {
      $pOnlineIdentity->setName($pObject->screen_name);
    }

    $pOnlineIdentity->setPhoto($pObject->profile_image_url);
    $pOnlineIdentity->setSocialPublishingEnabled(true);

    $pOnlineIdentity->save();
  }
}