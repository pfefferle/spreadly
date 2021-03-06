<?php
/**
 * Enter description here...
 *
 * @author Matthias Pfefferle
 */
class LinkedinAuthApiClient extends AuthApi {

  protected $aCommunity = "linkedin";

  /**
   * start the sign in process
   *
   * @author Matthias Pfefferle
   * @param myUser $pSessionUser
   * @param AuthToken $pAuthToken
   */
  public function doSignin($pSessionUser, $pOAuthToken) {
    $lAccessToken = $this->getAccessToken($pOAuthToken);
    //var_dump($lAccessToken);die();
    // get params
    $lParams = $lAccessToken->params;
    $lParamsArray = array();
    // extract params
    parse_str($lParams, $lParamsArray);
    //$lConsumer = new OAuthConsumer(sfConfig::get("app_linkedin_oauth_token"), sfConfig::get("app_linkedin_oauth_secret"));


    $lXml = OAuthClient::get($this->getConsumer(), $lParamsArray['oauth_token'], $lParamsArray['oauth_token_secret'], "http://api.linkedin.com/v1/people/~:(id,site-standard-profile-request,summary,picture-url,first-name,last-name,date-of-birth,location)");
    $lProfileArray = XmlUtils::XML2Array($lXml);
    //var_dump($lProfileArray);die();
    // linkedin identifier
    $lIdentifier = $lProfileArray['site-standard-profile-request']['url'];
    $lLinkedInId = $lProfileArray['id'];
    $lAuthIdentifier = "http://www.linkedin.com/profile/view?id=".$lLinkedInId;

    // ask for online identity
    $lOnlineIdentity = OnlineIdentityTable::retrieveByAuthIdentifier($lAuthIdentifier);

    // check if user already exists
    if ($lOnlineIdentity) {
      $lUser = $lOnlineIdentity->getUser();
    } else {
      $lOnlineIdentity = OnlineIdentityTable::addOnlineIdentity($lAuthIdentifier, $lLinkedInId, $this->aCommunityId, $lAuthIdentifier);

      // if there is no online identity die!
      if (!$lOnlineIdentity) {
        throw new sfException("incorrect online identity", 3);
      }

      // generate empty user
      $lUser = new User();
    }

    if (!$lUser || !$lUser->getId()) {
      $this->completeUser($lUser, $lProfileArray);
    	$this->completeOnlineIdentity($lOnlineIdentity, $lProfileArray, $lUser);
    }

    // import contacts
    //$this->importContacts($lOnlineIdentity->getId());

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

    $lXml = OAuthClient::get($this->getConsumer(), $lParamsArray['oauth_token'], $lParamsArray['oauth_token_secret'], "http://api.linkedin.com/v1/people/~:(id,site-standard-profile-request,summary,picture-url,first-name,last-name,date-of-birth,location)");
    $lProfileArray = XmlUtils::XML2Array($lXml);
    //var_dump($lProfileArray);die();
    // identifier
    $lLinkedInId = $lProfileArray['id'];
    $lProfileUri = "http://www.linkedin.com/profile/view?id=".$lLinkedInId;

    // ask for online identity
    $lOnlineIdentity = OnlineIdentityTable::retrieveByAuthIdentifier($lProfileUri);

    // check if user already exists
    if ($lOnlineIdentity) {
      if ($lOnlineIdentity->getUserId() && ($pUser->getId() == $lOnlineIdentity->getUserId())) {
        if (!$lOnlineIdentity->getActive()) {
          $lOnlineIdentity->setActive(true);
        } else {
          throw new sfException("online identity already added", 1);
        }
      } elseif ($lOnlineIdentity->getUserId() && ($pUser->getId() != $lOnlineIdentity->getUserId())) {
        throw new sfException("online identity already added by someone else", 2);
      }
    } else {
      // new online identity if no exist
      $lOnlineIdentity = OnlineIdentityTable::addOnlineIdentity($lProfileUri, $lLinkedInId, $this->aCommunityId, $lProfileUri);
    }

    $this->completeOnlineIdentity($lOnlineIdentity, $lProfileArray, $pUser);

    AuthTokenTable::saveToken($pUser->getId(), $lOnlineIdentity->getId(), $lParamsArray['oauth_token'], $lParamsArray['oauth_token_secret'], true);

    return $lOnlineIdentity;
  }

  /**
   * ask linkedin for an access-key
   *
   * @author Matthias Pfefferle
   * @return OAuthToken
   */
  public function getRequestToken() {
    $lRequestToken = OAuthClient::getRequestToken($this->getConsumer(), "https://api.linkedin.com/uas/oauth/requestToken", 'GET', array("oauth_callback" => $this->getCallbackUri()));

    // save the request token
    OauthRequestTokenTable::saveToken($lRequestToken, $this->getCommunity());

    return $lRequestToken;
  }

  /**
   * do the athentication on linkedin and redirect to linkedin
   *
   * @author Matthias Pfefferle
   * @link https://www.linkedin.com/uas/oauth/authenticate
   */
  public function doAuthentication() {
    $lRequestToken = self::getRequestToken();
    $lRequest = OAuthClient::prepareRequest($this->getConsumer(), $lRequestToken, "GET", "https://www.linkedin.com/uas/oauth/authenticate");
    // redirect
    header("Location: " . $lRequest->to_url());
    // do nothing more
    exit;
  }

  /**
   * ask linkedin for an access token
   *
   * @author Matthias Pfefferle
   * @param string $pTokenKey
   */
  public function getAccessToken($pOAuthToken) {
    $lAccessToken = OAuthClient::getAccessToken($this->getConsumer(), "https://api.linkedin.com/uas/oauth/accessToken", $pOAuthToken, "GET", array("oauth_verifier" => $pOAuthToken->verifier));

    return $lAccessToken;
  }

  /**
   * complete the user with the api json
   *
   * @param User $pUser
   * @param Object $pObject
   */
  public function completeUser(&$pUser, $lProfileArray) {
    $pUser->setUsername(UserUtils::getUniqueUsername(StringUtils::normalizeUsername($lProfileArray['first-name'].$lProfileArray['last-name'])));
    if(isset($lProfileArray['summary'])) {
      $pUser->setDescription(strip_tags($lProfileArray['summary']));
    }
    $pUser->setActive(true);
    $pUser->setAgb(true);
    $pUser->setFirstname($lProfileArray['first-name']);
    $pUser->setLastname($lProfileArray['last-name']);

    if(isset($lProfileArray['location']['country']['code'])) {
      $pUser->setCulture($lProfileArray['location']['country']['code']);
    }
    $pUser->save();
  }

  /**
   * complete the online-identity with the api json
   *
   * @param OnlineIdentity $pOnlineIdentity
   * @param Object $pObject
   */
  public function completeOnlineIdentity(&$pOnlineIdentity, $pProfileArray, $pUser) {
    $pOnlineIdentity->setName($pProfileArray['first-name'] . " " . $pProfileArray['last-name']);
    $pOnlineIdentity->setSocialPublishingEnabled(true);
    $pOnlineIdentity->setUserId($pUser->getId());

    if (isset($pProfileArray['date-of-birth']['year'])) {
      $pOnlineIdentity->setBirthdate($pProfileArray['date-of-birth']['year'].'-'.$pProfileArray['date-of-birth']['month'].'-'.$pProfileArray['date-of-birth']['day']);
    }
    $pOnlineIdentity->setLocationRaw($pProfileArray['location']['name']);
    $pOnlineIdentity->setPhoto($pProfileArray['pictureUrl']);
    $pOnlineIdentity->save();

    // update user's birthdate if it's not set yet and provided by this identity
    if (is_null($pUser->getBirthdate()) && $pOnlineIdentity->getBirthdate()) {
      $pUser->setBirthdate($pOnlineIdentity->getBirthdate());
      $pUser->save();
    }
  }
}