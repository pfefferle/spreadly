<?php
require_once dirname(__file__).'/../../lib/BaseTestCase.php';

include dirname(__file__).'/../../../lib/helper/ChartDataHelper.php';

class ChartDataHelperTest extends BaseTestCase {
  
  public static function setUpBeforeClass() {
    date_default_timezone_set('Europe/Berlin');
  }

  public function setUp() {
    parent::resetMongo();
    sfConfig::set('sf_environment', 'test');
    Doctrine::loadData(dirname(__file__).'/fixtures');
    sfConfig::set('sf_environment', 'dev');
    $this->from = date('Y-m-d', strtotime("today"));
    $this->to = date('Y-m-d', strtotime("tomorrow"));
  }
  
  public function testGetActivityData() {
    $data = MongoUtils::getActivityData("www.missmotz.de", $this->from, $this->to, 'daily');

    $this->assertEquals(5000, $data['data'][0]['facebook']['likes']);
    $this->assertEquals(0, $data['data'][0]['facebook']['dislikes']);
    $this->assertEquals(1000, $data['data'][0]['twitter']['clickbacks']);
    
    $this->assertEquals(1000, $data['pis'][0]['total']);
    $this->assertEquals(0, $data['pis'][0]['cb']);
    $this->assertEquals(1000, $data['pis'][0]['yiid']);

    $this->assertEquals('www.missmotz.de', $data['filter']['domain']);
    $this->assertEquals($this->from, $data['filter']['fromDate']);
    $this->assertEquals($this->to, $data['filter']['toDate']);
    $this->assertEquals('daily', $data['filter']['aggregation']);
  }

  public function testGetChartLineActivitiesData() {
    $data = json_decode(getChartLineActivitiesData(MongoUtils::getActivityData("www.missmotz.de", $this->from, $this->to, 'daily')));
    $this->assertEquals(9000, $data->likes[0]);

    $data = json_decode(getChartLineActivitiesData(MongoUtils::getActivityData("www.missmotz.de", $this->from, $this->to, 'daily'), 'facebook'));
    $this->assertEquals(5000, $data->likes[0]);
  }
  
  public function testGetAgeData() {
    $data = MongoUtils::getAgeData("www.missmotz.de", $this->from, $this->to, 'daily');

    $this->assertEquals(0, $data['data'][0]['u_18']);
    $this->assertEquals(0, $data['data'][0]['b_18_24']);
    $this->assertEquals(0, $data['data'][0]['b_25_34']);
    $this->assertEquals(0, $data['data'][0]['b_35_54']);
    $this->assertEquals(1000, $data['data'][0]['o_55']);

    $this->assertEquals('www.missmotz.de', $data['filter']['domain']);
    $this->assertEquals($this->from, $data['filter']['fromDate']);
    $this->assertEquals($this->to, $data['filter']['toDate']);
    $this->assertEquals('daily', $data['filter']['aggregation']);
  }
}
