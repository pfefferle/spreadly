<?php

/**
 * DomainProfile form.
 *
 * @package    yiid_stats
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class DomainProfileForm extends BaseDomainProfileForm
{
  public function configure()
  {
  	$lI18n = sfContext::getInstance()->getI18N();
    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'url'                => new sfValidatorAnd(array(
          new UrlValidator(array('required' => true, 'trim' => true), array('required' => $lI18n->__('Domain profile url required'))),
          new sfValidatorString(array('max_length' => 255, 'required' => true, 'trim' => true), array('max_length' => 'max_length', 'required' => $lI18n->__('Domain profile url required'))))),
      'protocol'              => new sfValidatorChoice(array('choices' => array(0 => 'http', 1 => 'https'), 'required' => true)),
      'state'              => new sfValidatorChoice(array('choices' => array(0 => 'pending', 1 => 'verified'), 'required' => false)),
      'sf_guard_user_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('sfGuardUser'))),
      'verification_count' => new sfValidatorInteger(array('required' => false))
    ));

  }
}
