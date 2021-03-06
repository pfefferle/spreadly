<?php

/**
 * analaytics actions.
 *
 * @package    yiid
 * @subpackage analaytics
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class analyticsActions extends sfActions {

  /**
   * initialize variables and check authentication
   *
   * (non-PHPdoc)
   * @see cache/frontend/prod/config/sfAction#initialize()
   */
  public function preExecute() {
    $request = $this->getRequest();
		sfContext::getInstance()->getConfiguration()->loadHelpers('Date');
    $this->pDomainProfileId = $request->getParameter('domainid', null);

    // check if user is allowed to see the statistics page
    if($this->pDomainProfileId) {
      $this->pDomainProfile = DomainProfileTable::getInstance()->find($this->pDomainProfileId);
      $this->forward404Unless($this->pDomainProfile && ($this->pDomainProfile->getSfGuardUserId() == $this->getUser()->getUserId()));
    }

    $this->pAggregation = $request->getParameter('aggregation', 'daily');
    $this->pDateFrom = $request->getParameter('date-from', date('Y-m-d', strtotime("6 days ago")));
    $this->pDateTo = $request->getParameter('date-to', date('Y-m-d'));
    $this->pCommunity = $request->getParameter('com', 'all');
    $this->pUrl = str_replace(" ", "+", urldecode($request->getParameter('url', null)));
    $this->pType = $request->getParameter('type', 'url_activities');
  }

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request) {
    $this->getResponse()->setSlot('js_document_ready', $this->getPartial('analytics/init_analytics.js'));
  	$this->pVerifiedDomains = DomainProfileTable::retrieveVerifiedForUser($this->getUser()->getGuardUser());
    $domainUrls = array();
    foreach ($this->pVerifiedDomains as $domain) {
      $domainUrls[] = $domain->getUrl();
    }

  	$hostRepo = MongoManager::getStatsDM()->getRepository('Documents\ActivityStats');
    $this->last30ByHost = $hostRepo->findLast30($domainUrls);
    if($this->last30ByHost) {
      $this->last30ByHost = $this->last30ByHost->toArray();
    }

  	$urlRepo = MongoManager::getStatsDM()->getRepository('Documents\ActivityUrlStats');
    $this->last30ByUrl = $urlRepo->findLast30($domainUrls);
    if($this->last30ByUrl) {
      $this->last30ByUrl = $this->last30ByUrl->toArray();
    }
  }

  public function executeDomain_statistics(sfWebRequest $request) {
    $this->getResponse()->setSlot('js_document_ready', $this->getPartial('analytics/init_analytics.js'));
    $dm = MongoManager::getStatsDM();
  	$pHost = $dm->getRepository("Documents\HostSummary")->findOneBy(array("host" => $this->pDomainProfile->getUrl()));

  	if ($pHost) {
  	  $this->pHost = $pHost;
  	} else {
      $this->setTemplate("no_stats");
  	}

    $dm = MongoManager::getStatsDM();
    $this->urls = $dm->getRepository("Documents\UrlSummary")->findBy(array("host" => $this->pDomainProfile->getUrl()))->limit(10)->sort(array("l" => "DESC", "c" => "DESC"));

  }

  public function executeDomain_detail(sfWebRequest $request){
    $this->getResponse()->setSlot('js_document_ready', $this->getPartial('analytics/init_analytics_details.js'));
  }

  public function executeGet_domain_detail(sfWebRequest $request) {
    $selector = $request->getParameter("date-selector");

    switch ($selector) {
      case "now":
      case "yesterday":
        $request->setParameter("date-to", date("Y-m-d", strtotime($selector)));
        break;
      case "7":
      case "30":
        $request->setParameter("date-to", date("Y-m-d", strtotime("yesterday")));
        $request->setParameter("date-from", date("Y-m-d", strtotime($selector." days ago")));
        break;
    }

    if ($request->getParameter("date-from")) {
      $this->forward('analytics', 'get_domain_detail_by_range');
    } else {
      $this->forward('analytics', 'get_domain_detail_by_day');
    }
  }

  public function executeGet_domain_detail_by_day(sfWebRequest $request) {
    $this->getResponse()->setContentType('application/json');
    $lDm = MongoManager::getStatsDM();
    $day = new MongoDate(strtotime($request->getParameter("date-to")));
    $lUrls = $lDm->getRepository("Documents\ActivityUrlStats")->findBy(array("host" => $this->pDomainProfile->getUrl(), "day" => $day));
    $lHostSummary = $lDm->getRepository("Documents\ActivityStats")->findOneBy(array("host" => $this->pDomainProfile->getUrl(), "day" => $day));
		//sfContext::getInstance()->getConfiguration()->loadHelpers('Date');
    $lReturn['content'] = $this->getPartial('analytics/domain_detail_content_by_day', array('pUrls' => $lUrls, 'pHostSummary' => $lHostSummary, 'pDomainProfile' => $this->pDomainProfile, 'showdate' => format_date(strtotime($request->getParameter('date-to')))));

    return $this->renderText(json_encode($lReturn));
  }

  public function executeGet_domain_detail_by_range(sfWebRequest $request) {
    $this->getResponse()->setContentType('application/json');

    $from = $request->getParameter("date-from");
    $to = $request->getParameter("date-to");

    $lDm = MongoManager::getStatsDM();

    //$lHost = $lDm->getRepository("Documents\HostSummary")->findOneBy(array("host" => $lDomainProfile->getUrl()));
    $lHost = $lDm->getRepository("Documents\ActivityStats")->findByRange(array($this->pDomainProfile->getUrl()), $from, $to);
    if($lHost) {
      $lHost = $lHost->toArray();
      if(count($lHost) > 1) {
        $lHost = $lHost[0];
      }
    }

    $lUrls = $lDm->getRepository("Documents\ActivityUrlStats")->findBy(
      array("host" => $this->pDomainProfile->getUrl(),
            "day" => array('$gte' => new MongoDate(strtotime($from)), '$lte' => new MongoDate(strtotime($to)))
            )
      );

    $lHostsRange = $lDm->getRepository("Documents\ActivityStats")->findBy(
      array("host" => $this->pDomainProfile->getUrl(),
            "day" => array('$gte' => new MongoDate(strtotime($from)), '$lte' => new MongoDate(strtotime($to)))
            )
      );

    $lHostsRange = $lHostsRange->toArray();

    $lLikesRange = array_values($this->padLikes($lHostsRange, $from, $to));
		//sfContext::getInstance()->getConfiguration()->loadHelpers('Date');
    $lReturn['content'] = $this->getPartial('analytics/domain_detail_content_by_range', array('pUrls' => $lUrls, 'pHostSummary' => $lHost, 'pDomainProfile' => $this->pDomainProfile, 'pLikes' => $lLikesRange, 'pStartDay' => $from, 'showdate' => format_date(strtotime($from)).' to '.format_date(strtotime($to))));
    return $this->renderText(json_encode($lReturn));
  }

  private function padLikes($activityStats, $from, $to) {
    $from = new DateTime($from);
    $from->setTime(0,0);
    $to = new DateTime($to);
    $to->setTime(24,0);

    $lDayPeriod = new DatePeriod($from, new DateInterval('P1D'), $to);

    $res = array();
    foreach ($lDayPeriod as $day) {
      $res[$day->format('Y-m-d')] = 0;
    }
    foreach ($activityStats as $stat) {
      $res[$stat->getDay()->format('Y-m-d')] = $stat->getLikes();
    }
    return $res;
  }

  public function executeUrl_detail(sfWebRequest $request){
    $this->getResponse()->setSlot('js_document_ready', $this->getPartial('analytics/init_analytics_details.js'));
    $this->selector = $request->getParameter("date-selector", null);
    $this->datefrom = $request->getParameter('date-from', null);
    $this->dateto = $request->getParameter('date-to', null);
    $this->pUrl = $request->getParameter('url');
  }

  public function executeGet_url_detail(sfWebRequest $request) {
    $selector = $request->getParameter("date-selector");

    switch ($selector) {
      case "now":
      case "yesterday":
        $request->setParameter("date-to", date("Y-m-d", strtotime($selector)));
        break;
      case "7":
      case "30":
        $request->setParameter("date-to", date("Y-m-d", strtotime("yesterday")));
        $request->setParameter("date-from", date("Y-m-d", strtotime($selector." days ago")));
        break;
    }

    if ($request->getParameter("date-from")) {
      $this->forward('analytics', 'get_url_detail_by_range');
    } else {
      $this->forward('analytics', 'get_url_detail_by_day');
    }
  }

  public function executeGet_url_detail_by_day(sfWebRequest $request) {
    $this->getResponse()->setContentType('application/json');
    $lDm = MongoManager::getStatsDM();

    $day = new MongoDate(strtotime(date("Y-m-d", strtotime($request->getParameter("date-to")))));
    $lUrls = $lDm->getRepository("Documents\AnalyticsActivity")->findBy(array("url" => $this->pUrl, "day" => $day, "d_id" => array('$exists' => false)));
    $lUrlSummary = $lDm->getRepository("Documents\ActivityUrlStats")->findOneBy(array("url" => $this->pUrl, "day" => $day));
    $lReturn['content'] = $this->getPartial('analytics/url_detail_content_by_day', array('pUrl' => $this->pUrl, 'pUrls' => $lUrls, 'pUrlSummary' => $lUrlSummary, 'pDomainProfile' => $this->pDomainProfile, 'showdate' => format_date($request->getParameter('date-to'))));

    return $this->renderText(json_encode($lReturn));
  }

  public function executeGet_url_detail_by_range(sfWebRequest $request) {
    $this->getResponse()->setContentType('application/json');

    $from = $request->getParameter("date-from");
    $to = $request->getParameter("date-to");

    $lDm = MongoManager::getStatsDM();

    $lUrlSummary = $lDm->getRepository("Documents\ActivityUrlStats")->findByUrlsAndRange(array($this->pUrl), $from, $to);
    if($lUrlSummary) {
      $lUrlSummary = $lUrlSummary->toArray();
      if(count($lUrlSummary) > 1) {
        $lUrlSummary = $lUrlSummary[0];
      }
    }

    $lUrls = $lDm->getRepository("Documents\AnalyticsActivity")->findBy(
      array("url" => $this->pUrl,
            "day" => array('$gte' => new MongoDate(strtotime($from)), '$lte' => new MongoDate(strtotime($to)))
            )
      );

    $lUrlsRange = $lDm->getRepository("Documents\ActivityUrlStats")->findBy(
      array("url" => $this->pUrl,
            "day" => array('$gte' => new MongoDate(strtotime($from)), '$lte' => new MongoDate(strtotime($to)))
            )
      );
    $lUrlsRange = $lUrlsRange->toArray();

    $lLikesRange = array_values($this->padLikes($lUrlsRange, $from, $to));

    $lReturn['content'] = $this->getPartial('analytics/url_detail_content_by_range', array('pUrl' => $this->pUrl, 'pUrls' => $lUrls, 'pUrlSummary' => $lUrlSummary, 'pDomainProfile' => $this->pDomainProfile, 'showdate' => $from.'-'.$to, 'pLikes' => $lLikesRange, 'pStartDay' => $from, 'showdate' => format_date(strtotime($from)).' to '.format_date(strtotime($to))));
    return $this->renderText(json_encode($lReturn));
  }

  public function executeSelect_period(sfWebRequest $request) {
		$this->pDomainId = $request->getParameter('domainid');
		$this->pUrl = $request->getParameter('url');
  }
}