<?php

/**
 * popup actions.
 *
 * @package    yiid
 * @subpackage popup
 * @author     Matthias Pfefferle
 * @author     Christian Schätzle
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class popupActions extends sfActions {

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeSignin(sfWebRequest $request) {
    $this->getResponse()->setSlot('js_document_ready', $this->getPartial('popup/js_popup_ready'));
    if ($this->getUser()->isAuthenticated()) {
      $this->redirect("@settings");
    }

    sfProjectConfiguration::getActive()->loadHelpers('I18N');
    $this->getUser()->setFlash('headline', __('SETTINGS', null, 'widget'));


    /* Uncomment for displaying error
     $this->getUser()->setFlash('headline', 'Login');
     $this->getUser()->setFlash('error', 'You have been logged out.');
     $this->getUser()->setFlash('error_msg', 'Please choose the same service you have used the last time to log in again.');
     */
  }

  public function executeSignout(sfWebRequest $request) {
    $this->getUser()->signOut();
    $this->redirect('@signin');
  }

  public function executeSigninto(sfWebRequest $request) {
  // if the user is already loged in, redirect to the stream
    if ($lService = $request->getParameter("service")) {
      $lObject = AuthApiFactory::factory($lService);
      $lObject->doAuthentication();
    } else {
      $this->redirect('@signin');
    }
  }

  public function executeComplete_signin(sfWebRequest $request) {
    if ($lToken = $request->getParameter('oauth_token')) {
      $lToken = OauthRequestTokenTable::retrieveByTokenKey($lToken);
      $lToken = $lToken->toOAuthToken();
      $lToken->verifier = $request->getParameter('oauth_verifier');
    } elseif ($lToken = $request->getParameter('code')) {
      // do nothing
    }

    $lObject = AuthApiFactory::factory($request->getParameter('service'));

    // check if it is a signin/signup or if the user wants
    // to add a new online-identity
    if ($this->getUser()->isAuthenticated() && $this->getUser()->getUserId()) {
      //$lObject->addIdentifier($this->getUser()->getUser(), $lToken);
    } else {
      //$lUser = $lObject->doSignin($this->getUser(), $lToken);
      //$this->getUser()->signIn($lUser);
      //UserRelationTable::doShit($lUser->getId());
    }

    //$this->redirect('@settings');
  }

  public function executeCreate_account(sfWebRequest $request) {

  }

  public function executeError(sfWebRequest $request) {
    $this->getResponse()->setSlot('js_document_ready', $this->getPartial('popup/js_popup_ready'));
    sfProjectConfiguration::getActive()->loadHelpers('I18N');
    $this->getUser()->setFlash('headline', __('ERROR', null, 'widget'));

    $this->getUser()->setFlash('error', 'Folgede Fehler sind aufgetreten:');
  }

  public function executeSettings(sfWebRequest $request) {
    $this->getResponse()->setSlot('js_document_ready', $this->getPartial('popup/js_popup_ready'));

    $lUser = $this->getUser()->getUser();
    if($request->getMethod() == sfRequest::POST) {
      $checkedOnlineIdentities = $request->getParameter('enabled_services');
      OnlineIdentityPeer::toggleSocialPublishingStatus(UserIdentityConPeer::getOnlineIdentityIdsByUserId($this->getUser()->getId()), $checkedOnlineIdentities);

      $lTempData = YiidActivityPeer::getTemporaryData($_COOKIE["yiid_temp_hash"]);
      setcookie("yiid_temp_hash", '', time()-3600, '/', sfConfig::get('app_settings_host'));

      if ($lTempData) {
        $lStatus = YiidActivityPeer::saveLikeActivitys($this->getUser()->getId(),
                                            $lTempData["url"],
                                            UserIdentityConPeer::getOnlineIdentityIdsByUserId($this->getUser()->getId()),
                                            $this->getUser()->getUser()->getOiIdsForLikeWidget(),
                                            $lTempData["score"],
                                            $lTempData["verb"],
                                            $lTempData["title"],
                                            $lTempData["description"],
                                            $lTempData["photo"]);
      }

      $this->getUser()->setAttribute("yiid_temp_hash", null);

     $this->getUser()->setFlash("onload", "window.close();", false);
    }
    $this->pIdentities = $lUser->getOnlineIdentitiesForLikeWidget();

    CookieUtils::generateWidgetIdentityCookie($this->pIdentities);
    sfProjectConfiguration::getActive()->loadHelpers('I18N');
    $this->getUser()->setFlash('headline', __('SETTINGS', null, 'widget'));
    $this->setLayout('layout_twocol');
  }

  public function executeAdd_service(sfWebRequest $request) {
    $this->getResponse()->setSlot('js_document_ready', $this->getPartial('popup/js_popup_ready'));
    $lUser = $this->getUser()->getUser();

    if ($lToken = $request->getParameter('token')) {
      $lRpxClient = new RpxClient();
      $lRpxClient->authenticate($lToken);
    } else {
      $lRpxClient = PersistentVariablePeer::get($this->getUser()->getAttribute('rpx_client', null, 'auth'));
    }

    if ($lRpxClient) {
      try {
        sfContext::getInstance()->getLogger()->debug('{rpxOpenID}: RPX authentication');
        $lRpxClient->doSigninTasks($lUser, true);
        sfContext::getInstance()->getLogger()->debug('{rpxOpenID}: RPX signin');

        // remove session and persistant variable
        PersistentVariablePeer::remove($this->getUser()->getAttribute('rpx_client', null, 'auth'));
        $this->getUser()->setAttribute('rpx_client', null, 'auth');

        // update cookie
        CookieUtils::generateWidgetIdentityCookie($lUser->getOnlineIdentitiesForPublishing());
      } catch (Exception $e) {
        sfContext::getInstance()->getLogger()->err('{Exception}: '.$e->getMessage());
        $this->getUser()->setFlash('error', "PROFILE_ALREADY_CREATED");
      }
    }

    $this->redirect("@settings");
  }
}
