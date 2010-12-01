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
  
  public function getRemainingCouponCount() {
    return $this->isUnlimited() ? 'unlimited' : $this->getCouponQuantity()-$this->getCouponClaimedQuantity();
  }
  
  public function getClaimedCouponCount() {
    return $this->getCouponClaimedQuantity();
  }
  
  private function fireQuantityChangedEvent() {
    $prefix = $this->getTable()->getTableName();
    $eventName = $prefix.".couponQuantityChanged";
    
    sfContext::getInstance()
      ->getEventDispatcher()
      ->notify(new sfEvent($this, $eventName, array("quantity" => $this->getCouponQuantity())));
  }
  
  public function setCouponQuantity($pQuantity) {
    parent::_set('coupon_quantity', $pQuantity);
    $this->fireQuantityChangedEvent();
  }
  
  public function popCoupon() {
    $code = null;
    if($this->isUnlimited()) {
      $coupons = $this->getCoupons();
      $code = $coupons[0]->getCode();
      $this->setCouponClaimedQuantity($this->getCouponClaimedQuantity()+1);
    } elseif($this->getRemainingCouponCount()>0) {
      $coupons = $this->getCoupons();
      $code = $coupons[0]->getCode();
      $this->setCouponClaimedQuantity($this->getCouponClaimedQuantity()+1);
      if($this->getCouponType()==DealTable::COUPON_TYPE_MULTIPLE) {
        $coupons[0]->delete();
        unset($coupons[0]);
      }
    }
    $this->save();
    return $code;
  }
  
  public function isUnlimited() {
    return $this->getCouponType()==DealTable::COUPON_TYPE_SINGLE &&
           $this->getCouponQuantity()==DealTable::COUPON_QUANTITY_UNLIMITED;
  }
  
  private function saveMultipleCoupons($params, $pIsAdding=false) {
    $codes = array();
    if($this->getCouponType()==DealTable::COUPON_TYPE_SINGLE) {
      if(!empty($params['single_code'])) {
        $codes[] = $params['single_code'];
      }
    } elseif($this->getCouponType()==DealTable::COUPON_TYPE_MULTIPLE) {
      // Convert line breaks to commas
      $couponString = preg_replace('/\n/', ',', $params['multiple_codes']);
      // Remove all remaining white space
      $couponString = preg_replace('/\s/', '', $couponString);
      $codes = explode(',', $couponString);
    }
    
    foreach ($codes as $code) {
      if(!empty($code)) {
        $c = new Coupon();
        $c->setCode($code);
        $c->setDealId($this->getId());
        $c->save();        
      }
    }
    
    $this->saveQuantities(count($codes), (empty($params['quantity']) ? 0 : $params['quantity']), $pIsAdding);

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
    $array['start_date'] = new MongoDate(strtotime($array['start_date']));
    $array['end_date'] = new MongoDate(strtotime($array['end_date']));
    $array['created_at'] = new MongoDate(strtotime($array['created_at']));
    $array['updated_at'] = new MongoDate(strtotime($array['updated_at']));
    return $array;
  }
}
