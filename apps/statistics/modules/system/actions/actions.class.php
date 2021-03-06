<?php

/**
 * system actions.
 *
 * @package    yiid
 * @subpackage system
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class systemActions extends sfActions {
  public function executeChange_language(sfWebRequest $request) {
  	$form = new sfFormLanguage(
      $this->getUser(),
      array('languages' => array('en', 'de'))
    );

    $form->process($request);

    $referer = $this->getRequest()->getReferer();

    if( preg_match('/spreadly/', parse_url($referer, PHP_URL_HOST)) ) {
      return $this->redirect( $referer );
    } else {
      return $this->redirect( 'index/index' );
    }
  }

  public function executeUpdate_icon_language(sfWebRequest $request) {
  	$lLanguage = $request->getParameter('lang');

  	$this->getUser()->setCulture($lLanguage);

    $referer = $this->getRequest()->getReferer();

    if( preg_match('/spreadly/', parse_url($referer, PHP_URL_HOST)) ) {
      return $this->redirect( $referer );
    } else {
      return $this->redirect( '@homepage' );
    }
  }

  public function executeCredentials(sfWebRequest $request) {
    $domainId = $request->getParameter("domainid");

    if (!$domainId) {
      $dealId = $request->getParameter("deal_id");
      $deal = DealTable::getInstance()->find($dealId);

      $domainId = $deal->getDomainProfileId();
    }

    $this->domainProfile = DomainProfileTable::getInstance()->find($domainId);
  }

  public function executeIndex(sfWebRequest $request) {}
  public function execute404(sfWebRequest $request) {}
  public function executeNodomain(sfWebRequest $request) {}
  public function executeImprint(sfWebRequest $request) {}
  public function executeTos(sfWebRequest $request) {}
  public function executePrivacy(sfWebRequest $request) {}
}
