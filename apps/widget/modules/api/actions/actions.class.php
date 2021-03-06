<?php
/**
 * api actions.
 *
 * @package    yiid
 * @subpackage api
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class apiActions extends sfActions {

	public function executeLoad_friends(sfWebRequest $request) {
	  $this->getResponse()->setHttpHeader('Access-Control-Allow-Origin', '*');

    $this->getResponse()->setContentType('text/html');
    $this->setLayout(false);
    $lUserId = $request->getParameter('u_id');
    $lSocialObjectId = $request->getParameter('so_id');

    $lReturn['success'] = false;
		$lReturn['html'] = false;
		$this->pFriends = array();
    if($lUserId && $lSocialObjectId) {
    	$this->pFriends = array_slice(UserTable::getFriendIdsBySocialObjectId($lSocialObjectId, $lUserId), 0, 3);
    }

   	return $lReturn['html'];
  }
  
  public function executeAds(sfWebRequest $request) {
    $id = $request->getParameter("id");
    
    $this->ad_code = '<script type="text/javascript"><!--
  google_ad_client = "ca-pub-1406192967534280";
  /* spreadly */
  google_ad_slot = "7458728780";
  google_ad_width = 250;
  google_ad_height = 250;
  //-->
</script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>';
    $this->setLayout(false);
    
    if (!$id) {
      return sfView::SUCCESS;
    }
    
    $dm = MongoManager::getDM();
    $ad = $dm->getRepository("Documents\Advertisement")->find(new MongoId($id));
    
    if (!$ad) {
      return sfView::SUCCESS;
    }
    
    $ad->save();
    
    $this->ad_code = $ad->getAdCode();
  }
  
  /**
   * 
   *
   * 
   */
  public function executeWidget(sfWebRequest $request) {
    // 
    $url = $request->getParameter("url");
    $this->url = $url;

    // extract host
    $host = parse_url($url, PHP_URL_HOST);
    
    // get most liked urls of the last 30 days
  	/*url_repo = MongoManager::getStatsDM()->getRepository('Documents\ActivityUrlStats');
    $this->last30 = $url_repo->findLast30OrderedAndLimited(array($host), 5);
    if($this->last30) {
      $this->last30 = $this->last30->toArray();
    }*/
    
    $this->urls = MongoManager::getStatsDM()->getRepository('Documents\AnalyticsActivity')->findLatestByHost($host, 6);
    
    // get latest users that liked one of the urls
    $this->activities = MongoManager::getStatsDM()->getRepository('Documents\AnalyticsActivity')->findLatestByHost($host, 9);
    
    // disable layout
    $this->setLayout(false);
  }
}