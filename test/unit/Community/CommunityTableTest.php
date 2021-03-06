<?php
require_once dirname(__file__).'/../../lib/BaseTestCase.php';

class  CommunityTableTest extends BaseTestCase {


  public static function setUpBeforeClass() {
    Doctrine::loadData(dirname(__file__).'/fixtures');
  }

  public function testRetrieveByDomain() {
    $result = CommunityTable::retrieveByDomain("google");

    $this->assertTrue(3 <= count($result));

    $lastCommunity = $result[count($result)-1];

    $this->assertEquals("website", $lastCommunity->getCommunity());
  }
}