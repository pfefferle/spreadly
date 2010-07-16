<?php
require_once dirname(__file__).'/../../lib/BaseTestCase.php';

class UserTest extends BaseTestCase {

  public static function setUpBeforeClass() {
    Doctrine::loadData(dirname(__file__).'/fixtures');
  }


  public function testGetOnlineIdentities() {
    $lUserHugo = UserTable::getByIdentifier('hugo');
    $lIdentities = $lUserHugo->getOnlineIdentities();
    $this->assertEquals(2, count($lIdentities));
    $this->assertTrue(is_array($lIdentities));
  }
}