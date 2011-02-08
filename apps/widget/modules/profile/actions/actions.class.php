<?php

/**
 * profile actions.
 *
 * @package    yiid
 * @subpackage profile
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class profileActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
		$this->pLatestActivities = YiidActivityTable::retrieveLatestActivitiesByContacts($this->getUser()->getUserId(), null, null, 30, 10, 4);
    $this->pHottestObjects = SocialObjectTable::retrieveHotObjets($this->getUser()->getUserId(), null, null, 30, 1, 4);
		$this->setLayout('layout');
  }
}
