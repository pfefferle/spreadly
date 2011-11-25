<?php

/**
 *
 * @author  karina
 */
class DealTypeValidator extends sfValidatorBase {
  public function configure($options = array(), $messages = array()) {

  	$this->addOption('type', 'type');
    $this->addOption('domain_profile_id', 'domain_profile_id');
    $this->setMessage('required', 'Required fields');
  }

  protected function doClean($values) {
  	$lType = $values['type'];

  	//if coupon type is code, check first if is empty. if, throw error. if not, set fields for coupon type: url/download to null
  	//if coupon type is url/download, check if fields are empty. if not, set fields for coupon type: code to null
  	if($lType == 'pool') {
  		$values['domain_profile_id'] = null;
  	} else{
  	 	if($this->isEmpty($values['domain_profile_id'])){
  			throw new sfValidatorErrorSchema($this, array(
  					$this->getOption('domain_profile_id') => new sfValidatorError($this, 'required'),
  				));
  		}
  	}

    return $values;
  }

}
