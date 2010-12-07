<?php

/**
 * Deal
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    yiid_stats
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Deal extends BaseDeal {

  public function addMoreCoupons($params) {
    return $this->saveMultipleCoupons($params, true);
  }

  public function saveInitialCoupons($params) {
    return $this->saveMultipleCoupons($params);
  }

  public function getRemainingCouponQuantity() {
    return $this->isUnlimited() ? 'unlimited' : $this->getCouponQuantity()-$this->getCouponClaimedQuantity();
  }

  public function getHumanCouponQuantity() {
    return $this->isUnlimited() ? 'unlimited' : $this->getCouponQuantity();
  }

  public function getActiveCssClass() {
    return ($this->isActive() ? 'deal_active' : 'deal_inactive');
  }
  
  public function getCssClasses() {
    return $this->getState().' '.($this->getState()=='approved' ? $this->getActiveCssClass() : '');
  }
  
  public function isActive() {
    $lNow = time();
    return $this->getState()=='approved' &&
           strtotime($this->getStartDate()) <= $lNow &&
           strtotime($this->getEndDate()) >= $lNow &&
           ($this->isUnlimited() || $this->getRemainingCouponQuantity()>0);
  }

  private function fireQuantityChangedEvent() {
    $prefix = $this->getTable()->getTableName();
    $eventName = $prefix.".couponQuantityChanged";
    sfProjectConfiguration::getActive()->getEventDispatcher()->notify(new sfEvent($this, $eventName, array("quantity" => $this->getCouponQuantity())));
  }

  public function setCouponQuantity($pQuantity) {
    parent::_set('coupon_quantity', $pQuantity);
    $this->fireQuantityChangedEvent();
  }

  public function setCouponClaimedQuantity($pQuantity) {
    parent::_set('coupon_claimed_quantity', $pQuantity);
    $this->fireQuantityChangedEvent();
  }

  public function popCoupon() {
    $code = null;
    $coupon = CouponTable::getInstance()->findOneByDealId($this->getId());

    if($coupon) {
      $code = $coupon->getCode();      
      if($this->getCouponType()==DealTable::COUPON_TYPE_MULTIPLE) {
        $coupon->delete();
      }
      $this->setCouponClaimedQuantity($this->getCouponClaimedQuantity()+1);
      $this->save();
    }
    
    return $code;
  }

  public function isUnlimited() {
    return $this->getCouponType()==DealTable::COUPON_TYPE_SINGLE &&
           $this->getCouponQuantity()==DealTable::COUPON_QUANTITY_UNLIMITED;
  }

  private function allElementsEmpty($array) {
    $lAllEmpty = true;
    foreach ($array as $element) {
      if(!empty($element)) {
        $lAllEmpty = false;
      }
    }
    return $lAllEmpty;
  }
  
  private static function compactArray($element) {
    return !empty($element);
  }

  private function saveMultipleCoupons($params, $pIsAdding=false) {
    $codes = array();
    if($this->getCouponType()==DealTable::COUPON_TYPE_SINGLE) {
      if(!empty($params['single_code'])) {
        $codes[] = $params['single_code'];
      }
    } elseif($this->getCouponType()==DealTable::COUPON_TYPE_MULTIPLE) {
      // Convert line breaks to commas
      $couponString = preg_replace('/[\r\n]+/', ',', $params['multiple_codes']);
      // Remove all remaining white space
      $couponString = preg_replace('/\s/', '', $couponString);
      $codes = explode(',', $couponString);
    }
    
    $codes = array_filter($codes, array('Deal', 'compactArray'));
    
    foreach ($codes as $code) {
      if(!empty($code)) {
        $c = new Coupon();
        $c->setCode($code);
        $c->setDealId($this->getId());
        $c->save();
      }
    }
    $this->saveQuantities(count($codes), (empty($params['quantity']) ? 0 : intval($params['quantity'])), $pIsAdding);

    return $this->getCouponQuantity();
  }

  private function saveQuantities($pNumberOfCodes, $pParamQuantity, $pIsAdding) {
    $lCouponQuantity = $this->getCouponQuantity();
    $lNewEntries = 0;

    if($pIsAdding && $this->getCouponType()==DealTable::COUPON_TYPE_SINGLE) {
      if(!$this->isUnlimited()) {
        $lNewEntries += $pParamQuantity;
      }
    } elseif($this->getCouponType()==DealTable::COUPON_TYPE_MULTIPLE) {
      $lNewEntries += $pNumberOfCodes;
    }

    $this->setCouponQuantity($lCouponQuantity+$lNewEntries);

    $this->save();
  }

  public function toMongoArray() {
    $array = $this->toArray();
    $array['id'] = intval($this->getId());
    $array['start_date'] = new MongoDate(intval(strtotime($array['start_date'])));
    $array['end_date'] = new MongoDate(intval(strtotime($array['end_date'])));
    $array['created_at'] = new MongoDate(intval(strtotime($array['created_at'])));
    $array['updated_at'] = new MongoDate(intval(strtotime($array['updated_at'])));
    $array['remaining_coupon_quantity'] = $this->getRemainingCouponQuantity();
    $array['human_coupon_quantity'] = $this->getHumanCouponQuantity();
    $array['is_unlimited'] = $this->isUnlimited();
    $array['host'] = $this->getDomainProfile()->getUrl();
    return $array;
  }
  
  public function validateNewQuantity($newQuantity) {
    $lError = "";
    if(!$this->isUnlimited()) {
      $lNumeric = is_numeric($newQuantity);
    	$lHigher = $newQuantity > $this->getCouponQuantity();
    	if(($lNumeric && $lHigher) || ($lNumeric && $newQuantity == $this->getCouponQuantity())) {
    	  // The new quantity is either numeric and higher or nothing was changed, so nothing should be done
    	} else {
    	  $lError = "";
    	  $lError = $lError. ($lNumeric ? '' : 'not a number');
    	  $lError = $lError.((!$lNumeric&&!$lHigher) ? ' and ' : '');
    	  $lError = $lError.($lHigher ? '' : 'not more than before');
    	}
    } else {
      $lError = "You can not change the quantity of unlimited coupons.";
    }
    
    return empty($lError) ? true : $lError;
  }
  
  public function validateNewEndDate($pDateString) {
    $lError = '';
    $lNewDate = strtotime($pDateString);
    $lCurrentDate = strtotime($this->getEndDate());
    $lNow = time();

    if($lNewDate <= $lNow) {
      $lError = "The new end date must be in the future";
    } else {
      $this->setEndDate(date("Y-m-d H:i:s", $lNewDate));
      
      if(DealTable::isOverlapping($this)) {
        $lError = "The new end date is overlapping another deal";
        $this->setEndDate(date("Y-m-d H:i:s", $lCurrentDate));
      }
    }
    return empty($lError) ? true : $lError;
  }  
}
