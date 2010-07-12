<?php
/**
 * User
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    yiid
 * @subpackage model
 * @author     Matthias Pfefferle
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class User extends BaseUser {

  /**
   * function to get the full name (first + last-name)
   *
   * @author Matthias Pfefferle
   * @return string fullname or username
   */
  public function getFullName() {
    if ($this->getFirstname() && $this->getLastname()) {
      return $this->getFirstname() . " " . $this->getLastname();
    } else {
      return $this->getUsername();
    }
  }

  /**
   * function to verify a password
   *
   * @param string $pPassword
   * @return boolean
   */
  public function verifyPassword( $pPassword  ) {
    $lHash = PasswordUtils::salt_password( md5($pPassword), $this->getSalt() );
    if( $lHash === $this->getPasswordhash() ){
      return true;
    }
    else{
      return false;
    }
  }

  /**
   * runs before User->save()
   *
   * @author Matthias Pfefferle
   * @param Doctrine_Event $pEvent
   */
  public function preSave($pEvent) {
    $this->setSortname($this->generateSortname());
  }

  /**
   * runs after User->insert()
   *
   * @author Matthias Pfefferle
   * @param Doctrine_Event $pEvent
   */
  public function postInsert($pEvent) {
    // @todo add yiid online identity
  }

  /**
   * constructs the sortname of the current user-object
   *
   * @author Matthias Pfefferle
   * @return string
   */
  public function generateSortname() {
    if ($this->getLastname() != '') {
      $lSortname = $this->getLastname().$this->getFirstname();
    }
    else {
      $lSortname = $this->getUsername();
    }

    if (preg_match('/^[^a-zA-Z]/',$lSortname)) {
      $lSortname = '#'.$lSortname;
    }
    return $lSortname;
  }
}