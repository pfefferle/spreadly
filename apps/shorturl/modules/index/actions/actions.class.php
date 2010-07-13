<?php

/**
 * index actions.
 *
 * @package    communipedia
 * @subpackage index
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class indexActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex($request) {
    $this->shortUrl = null;
    $this->form = new ShortUrlForm();

    if ($request->isMethod('post')) {
      $this->form->bind($request->getParameter('shorturl'));
      if ($this->form->isValid()) {
        if ($lShortUrl = ShortUrlPeer::retrieveByUrl($this->form->getValue('url'))) {
          $this->shortUrl = $lShortUrl->getShortedUrl();
        } else {
          $lShortUrl = $this->form->save();
          $this->shortUrl = $lShortUrl->getShortedUrl();
        }

        // add short_url_con
        if ($lUser = $this->getUser()->getUser()) {
          $lShortUrl->addUser($lUser->getId());

          // throw out an activity
          if ($request->getParameter('add_activity') === "on") {
            sfContext::getInstance()->getEventDispatcher()->notify(new ActivityEvent($lShortUrl, 'init_save_activity', ActivityObjectBookmark::VERB_SHARE, $lUser->getId()));
          }
        }
      }
    } elseif ($request->getParameter('url')) {
      $this->form->bind(array('url' => $request->getParameter('url')));
      if ($this->form->isValid()) {
        if ($lShortUrl = ShortUrlPeer::retrieveByUrl($this->form->getValue('url'))) {
          $this->shortUrl = $lShortUrl->getShortedUrl();
        } else {
          $lShortUrl = $this->form->save();
          $this->shortUrl = $lShortUrl->getShortedUrl();
        }
      }
    }

    $this->pShortUrls = ShortUrlPeer::getLatestUrls(5);
  }

  public function executeYiid_bar() {

  }

  public function executeShorturl($request) {
    $lIdentifier = $request->getParameter('identifier');

    $lPk = ShortUrlPeer::uncharize($lIdentifier);
    $lShortUrl = ShortUrlPeer::retrieveByPK($lPk);

    $this->forward404Unless($lShortUrl);

    // also support json output
    if ($request->getParameter('format') == 'json') {
      $this->getResponse()->setContentType('application/json');
      return $this->renderText(ShortUrlPeer::prepareApiResponse($lShortUrl->getUrl()));
    } else {
      // check if it is a yiid entry
      $this->redirect($lShortUrl->getUrl());
    }
  }

  /**
   * @author Matthias Pfefferle <matthias@pfefferle.org>
   */
  public function executeYiid_auth($request) {
    sfProjectConfiguration::getActive()->loadHelpers("YiidUrl", "Url");

    $lOAuthConsumer = new OAuthConsumer("cope", "ecfe19a8529c1095aae11c283a9370cdc931f08d", null);

    if ($lOAuthToken = $request->getParameter("oauth_token")) {
      $lOAuthSecret = PersistentVariablePeer::get("oauth-".$lOAuthToken);
      $lRequestToken = new OAuthToken($lOAuthToken, $lOAuthSecret);
      $lAccessToken = OAuthClient::getAccessToken($lOAuthConsumer, url_for_frontend("api/access", true), $lRequestToken, "GET", null, new OAuthSignatureMethod_HMAC_SHA1());

      $lToken = OAuthConsumerTokenPeer::find($lOAuthConsumer, "access", $lAccessToken->key);
      $lToken->delete();
      $lUser = UserPeer::retrieveByPK($lToken->getUserId());

      $this->getUser()->signIn($lUser);
      $this->redirect("index/index");
    } else {
      $lRequestToken = OAuthClient::getRequestToken($lOAuthConsumer, url_for_frontend("api/request", true), "GET", null, new OAuthSignatureMethod_HMAC_SHA1());

      PersistentVariablePeer::set("oauth-".$lRequestToken->key, $lRequestToken->secret);
      $lAuthUrl = url_for_frontend("api/authorize", true) . "?oauth_token=".$lRequestToken->key."&oauth_callback=".urlencode(url_for("index/yiid_auth", true)."?test=test");

      $this->redirect($lAuthUrl);
    }
  }

  // redirect the 404 to the frontend
  public function executeError404($request) {
    $this->redirect(sfConfig::get('app_settings_url').'/static/404');
  }
}
