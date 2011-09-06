<?php

/**
 * like actions.
 *
 * @package    yiid
 * @subpackage like
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class likeActions extends sfActions {
  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request) {
    $lUrl = $request->getParameter("url", null);

    if ($request->getMethod() == "POST") {
      $lParams = $request->getParameter('like');

      $lParams['u_id'] = $this->getUser()->getUserId();

      $lActivity = new Documents\YiidActivity();
      $lActivity->fromArray($lParams);
			$pError = null;
      // try to save activity
      try {
        $lActivity->save();
        $this->redirect("@deal?url=".urlencode($lUrl));
      } catch (Exception $e) { // send error on exception
        $this->getLogger()->err($e->getMessage());
        $this->pError = $e->getMessage();
      }
    }

    $dm = MongoManager::getDM();

    // ckeck url
    if ($lUrl) {
      $this->pActivity = $dm->getRepository("Documents\YiidActivity")->findOneBy(array("url" => $lUrl, "u_id" => intval($this->getUser()->getId()), "d_id" => array('$exists' => false)));

      // if user has already liked
      if($this->pActivity) {
        $this->redirect("@deal?url=".urlencode($lUrl));
      }
    }

    $lYiidMeta = new YiidMeta();
    $lYiidMeta->fromParams($request->getParameterHolder());
    $this->pYiidMeta = SocialObjectParser::fetch($request->getParameter("url"), $lYiidMeta);

    if ($this->pYiidMeta === false) {
      return $this->setTemplate("wrong_url");
    }

    $this->getResponse()->setSlot('js_document_ready', $this->getPartial('like/js_init_like.js', array('pImgCount' => count($this->pYiidMeta->getImages()), 'pUrl' => $request->getParameter("url"))));
  }

  public function executeGet_images(sfWebRequest $request) {
  	$this->getResponse()->setContentType('application/json');
    $lUrl = $request->getParameter("url");
    $lImages = ImageParser::fetch($lUrl);
    $lReturn['count'] = count($lImages);
    $lReturn['html'] = $this->getPartial('like/meta_images_list', array('pImages' => $lImages));
    return $this->renderText(json_encode($lReturn));
  }

  public function executeNourl(sfWebRequest $request){
    $this->getResponse()->setSlot('js_document_ready', $this->getPartial('like/js_init_nourl.js'));
  }

  public function executeGet_services(){
  	$this->getResponse()->setContentType('application/json');
    $lReturn['services'] = $this->getComponent('like','share_section');
    $lReturn['footer'] = $this->getPartial('global/footer');
    return $this->renderText(json_encode($lReturn));
  }
}