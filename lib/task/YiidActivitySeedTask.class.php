<?php

class YiidActivitySeedTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'statistics'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'yiid';
    $this->name             = 'activity-testdata';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [YiidActivitySeed|INFO] task does things.
Call it with:

  [php symfony YiidActivitySeed|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array()) {
    try {
      sfContext::getInstance();
    } catch (Exception $e) {
      // aize the database connection
      $configuration = ProjectConfiguration::getApplicationConfiguration($options['application'], $options['env'], true);
      sfContext::createInstance($configuration);
    }

    $databaseManager = new sfDatabaseManager($this->configuration);
    $databaseManager->loadConfiguration();
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $originalPostToServicesValue = sfConfig::get('app_settings_post_to_services');
    sfConfig::set('app_settings_post_to_services', 0);

    $this->log("Using mongo host: ".sfConfig::get('app_mongodb_host'));

    $lUserHugo = UserTable::retrieveByUsername('hugo');
    $lHugoOis = $lUserHugo->getOnlineIdentitesAsArray();

    $lUserJames = UserTable::retrieveByUsername('james');
    $lJamesOis = $lUserJames->getOnlineIdentitesAsArray();

    $lCommunityTwitter = CommunityTable::retrieveByCommunity('twitter');
    $lCommunityFb = CommunityTable::retrieveByCommunity('facebook');

    $lOiHugoTwitter = OnlineIdentityTable::retrieveByAuthIdentifier('http://twitter.com/account/profile?user_id=21092406', $lCommunityTwitter->getId());
    $lOiJamesFacebook = OnlineIdentityTable::retrieveByAuthIdentifier('james_fb', $lCommunityFb->getId());

    $urls = array('www.snirgel.de', 'notizblog.org', 'www.missmotz.de');
    $tags = array('geekstuff', 'otherthings', 'schuhe');
    $users = array($lUserHugo, $lUserJames);
    $services = array('facebook', 'twitter', 'linkedin', 'google');
    $deals = array('Campaign No. 1', 'Campaign No. 2', 'Campaign No. 3');
    $dm = MongoManager::getDM();

    for ($i=0; $i < 100; $i++) {
      $url = $this->oneOfThese($urls);
      $tag = $this->oneOfThese($tags);
      $user = $this->oneOfThese($users);
      $cb_ref = $this->oneOfThese(array('', '', '', '', '', '', 'http://tierscheisse.de'));
      $ra = $this->random(1000);
      $theC =  mt_rand(strtotime("3 days ago"),strtotime("today"));

      $array = array(
        'url' => "http://$url/$ra",
        'url_hash' => "hash.$ra",
        'u_id' => $user->getId(),
        'oiids' => $user->getOnlineIdentitesAsArray(),
        'tags' => $tag.$ra,
        'title' => "$url title",
        'descr' => "$url description",
        'comment' => "$url comment",
        'c' => $theC,
        'cb' => $this->randBoolean() ? $this->random(30) : 0,
        'cb_referer' => $cb_ref!='' ? $cb_ref : null,
        'cb_service' => $cb_ref!='' ? $this->oneOfThese($services) : null
      );

      $lActivity = new Documents\YiidActivity();
      $lActivity->fromArray($array);

      try {
        $lActivity->skipUrlCheck=true;
        $lActivity->save();
      } catch(Exception $e) {
        $this->log($e->getMessage());
      }
    }

    // same for deals
    for ($i=0; $i < 100; $i++) {
      $url = $this->oneOfThese($urls);
      $tag = $this->oneOfThese($tags);
      $user = $this->oneOfThese($users);
      $cb_ref = $this->oneOfThese(array('', '', '', '', '', '', 'http://tierscheisse.de'));
      $i_url = $this->oneOfThese(array('http://ard.de', 'http://bild.de', 'http://spiegel.de', 'http://tierscheisse.de'));
      $ra = $this->random(1000);
      $theC =  mt_rand(strtotime("3 days ago"),strtotime("today"));
      $deal = DealTable::getInstance()->findOneByName($this->oneOfThese($deals));

      $array = array(
        'url' => "http://$url/$ra",
        'url_hash' => "hash.$ra",
        'u_id' => $user->getId(),
        'oiids' => $user->getOnlineIdentitesAsArray(),
        'tags' => $tag.$ra,
        'title' => "$url title",
        'descr' => "$url description",
        'comment' => "$url comment",
        'c' => $theC,
        'd_id' => $deal->getId(),
        'cb' => $this->randBoolean() ? $this->random(30) : 0,
        'cb_referer' => $cb_ref!='' ? $cb_ref : null,
        'i_url' => $i_url,
        'cb_service' => $cb_ref!='' ? $this->oneOfThese($services) : null
      );

      $lActivity = new Documents\YiidActivity();
      $lActivity->fromArray($array);

      try {
        $lActivity->skipUrlCheck=true;
        $lActivity->save();
      } catch(Exception $e) {
        $this->log($e->getMessage());
      }
    }
    
    $ds = new Documents\DomainSettings();
    $ds->setDomain("blog.local");
    $ds->setMute(0);
    $ds->save();
		
    $ds = new Documents\DomainSettings();
    $ds->setDomain("pfefferle.org");
    $ds->setDisableAds(true);
    $ds->save();
    
    $ad = new Documents\Advertisement();
    $ad->setDomains(array("pfefferle.org", "notizblog.org", "www.spiegel.de", "blog.local"));
    $ad->setAdCode('<script type="text/javascript" src="http://a.ligatus.com/?ids=34548&t=js"></script>');
    $ad->setUpdatedAt(strtotime("now"));
    $ad->setStartingAt(strtotime("now"));
    $ad->setAdHeight(500);
    $ad->setAdWidth(50);
    $ad->save();
    
    $ad = new Documents\Advertisement();
    $ad->setDomains(array("blog.local"));
    $ad->setAdCode("<script type='text/javascript' src='http://imagesrv.adition.com/js/adition.js'></script>
<script type='text/javascript' src='http://ad4.adfarm1.adition.com/js?wp_id=744125'></script>");
    $ad->setUpdatedAt(strtotime("now"));
    $ad->setStartingAt(strtotime("now"));
    $ad->setAdHeight(100);
    $ad->setAdWidth(800);
    $ad->save();
    
    $ad = new Documents\Advertisement();
    $ad->setDomains(array("pfefferle.org", "notizblog.org", "www.spiegel.de", "blog.local"));
    $ad->setAdCode('<script type="text/javascript"><!--
  google_ad_client = "ca-pub-1406192967534280";
  /* spreadly */
  google_ad_slot = "7458728780";
  google_ad_width = 250;
  google_ad_height = 250;
  //-->
</script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>');
    $ad->setUpdatedAt(strtotime("now"));
    $ad->setStartingAt(strtotime("now"));
    $ad->setAdHeight(250);
    $ad->setAdWidth(250);
    $ad->save();

    /*

    $lActivity = new Documents\YiidActivity();
    $lActivity->fromArray($array);
    $lActivity->save();

    $array = array(
      'url' => "http://$url",
      'oiids' => array($lOiJamesFacebook->getId()),
      'title' => "$url deal title",
      'descr' => "$url description",
      'comment' => "$url comment",
      'thumb' => null,
      'clickback' => null,
      'tags' => null,
      'u_id' => $lUserJames->getId()
    );

    $lActivity = new Documents\YiidActivity();
    $lActivity->fromArray($array);
    $lActivity->save();

    if($deal[0]->canApprove()) {
      $deal[0]->approve();
    }


    $url = 'notizblog.org';
    $array = array(
      'url' => "http://$url",
      'oiids' => array($lOiHugoTwitter->getId()),
      'title' => "$url deal title",
      'descr' => "$url description",
      'comment' => "$url comment",
      'thumb' => null,
      'clickback' => null,
      'tags' => null,
      'u_id' => $lUserHugo->getId()
    );

    $lActivity = new Documents\YiidActivity();
    $lActivity->fromArray($array);
    $lActivity->save();

    $deal = DealTable::getInstance()->findByDescription('missmotz approved description');
    if($deal[0]->canApprove()) {
      $deal[0]->approve();
    }

    $url = 'www.missmotz.de';
    $array = array(
      'url' => "http://$url",
      'oiids' => array($lOiHugoTwitter->getId()),
      'title' => "$url deal title",
      'descr' => "$url description",
      'comment' => "$url comment",
      'thumb' => null,
      'clickback' => null,
      'tags' => "Schuhe, Hemden",
      'u_id' => $lUserHugo->getId()
    );

    $lActivity = new Documents\YiidActivity();
    $lActivity->fromArray($array);
    $lActivity->save();
    */

    sfConfig::set('app_settings_post_to_services', $originalPostToServicesValue);

    $this->generateErrorLog();
  }
  private function random($range) {
    return mt_rand(0,$range);
  }
  private function randBoolean() {
    return mt_rand(0,1)%2==0 ? true : false;
  }
  private function oneOfThese($these) {
    return $these[mt_rand(0, count($these)-1)];
  }

  public function generateErrorLog() {
    $lUserHugo = UserTable::retrieveByUsername('hugo');
    $lHugoOis = $lUserHugo->getOnlineIdentities();

    $lHugoOi = $lHugoOis[0];

    for ($i = 0; $i <= 100; $i++) {
      $lError = new Documents\ApiErrorLog();

      $lError->setCode($i);
      $lError->setMessage("I Pity the Fool");
      $lError->setOiId($lHugoOi->getId());
      $lError->setUId($lUserHugo->getId());

      $dm = MongoManager::getDM();
      $dm->persist($lError);
      $dm->flush();
    }
  }
}
