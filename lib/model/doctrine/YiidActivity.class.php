<?php

/**
 * YiidActivity
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    yiid
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class YiidActivity extends BaseYiidActivity {
  /**
   *  saves are handled by the related Table-Class as we save them in MongoDB
   * (non-PHPdoc)
   * @see lib/vendor/symfony/lib/plugins/sfDoctrinePlugin/lib/vendor/doctrine/Doctrine/Doctrine_Record::save()
   */
  public function save(Doctrine_Connection $conn = null) {
    $lObjectToSave = $this->toArray(false);
    $lObjectToSave['u_id'] = intval($lObjectToSave['u_id']);
    $lObjectToSave['d_id'] = intval($lObjectToSave['d_id']);
    $lObjectToSave['so_id'] = new MongoId($lObjectToSave['so_id']);
    unset($lObjectToSave['id']);
    if ($this->getId()) {
      $lObjectToSave = YiidActivityTable::updateObjectInMongoDb(array('_id' => new MongoId($this->getId())), $lObjectToSave);
      return false;
    } else {
      $lObjectToSave = YiidActivityTable::saveObjectToMongoDb($lObjectToSave);
      $this->setId($lObjectToSave['_id'].""); // cast mongoID to string
    }
    if ($lObjectToSave) {
      return $lObjectToSave;
    }
    return false;
  }

  /**
   * wrapper for checkeing on clickback
   * @return boolean
   */
  public function isClickback() {
    return $this->getCbReferer()?true:false;
  }

  /**
   * wrapper for checkeing on deal-activity
   * @return boolean
   */
  public function isDealActivity() {
    return $this->getDId()?true:false;
  }

  public function delete(Doctrine_Connection $con = null) {
    $this->collection->remove(array("_id" => new MongoId($this->getId()) ));
  }

  /**
   * at the moment this method returns the name of the first community
   *
   * @author Christian Schätzle
   */
  public function getCommunityNames() {
    $lObjectOiIds = $this->getCids();
    $lNamesArray = array();

    /*
    foreach($lObjectOiIds as $lId) {
      $lOi = Doctrine::getTable('OnlineIdentity')->find($lId);
      $lName = Doctrine::getTable('Community')->find($lOi->getCommunityId())->getName();
      array_push($lNamesArray, $lName);
    }

    return $lNamesArray;
    */

    return Doctrine::getTable('Community')->find($lObjectOiIds[0])->getName();
  }

  /**
   * This method returns the minuts/hours/day since publishing of this activity
   *
   * @author Christian Schätzle
   */
  public function getPublishingTime() {
    $lSocialObjectDate = $this->getC();

    sfProjectConfiguration::getActive()->loadHelpers(array('Date'));
    $lDate = distance_of_time_in_words($lSocialObjectDate);

    return $lDate;
  }

  public function getUser() {
    $lUser = UserTable::getInstance()->retrieveByPk($this->getUId());

    return $lUser;
  }

  /**
   * checks if it is a deal activity or not
   *
   * @return boolean
   */
  public function isDeal() {
    if ($this->getDId()) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * returns the matching deal infos
   *
   * @return Object|null
   */
  public function getDeal() {
    if ($this->getDId()) {
      return DealTable::getInstance()->find($this->getDId());
    } else {
      return null;
    }
  }
}
