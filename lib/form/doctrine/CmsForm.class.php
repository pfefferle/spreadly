<?php

/**
 * Cms form.
 *
 * @package    yiid
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CmsForm extends BaseCmsForm
{
  public function configure()
  {

    $lLanguages = LanguageTable::getAllLanguageCodesAsArray();

    $this->embedI18n($lLanguages);
    foreach ($lLanguages as $lLang) {
      $this->widgetSchema[$lLang]['title'] =  new sfWidgetFormTextareaTinyMCE();
    }

  }
}
