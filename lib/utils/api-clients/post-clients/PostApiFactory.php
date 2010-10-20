<?php
/**
 * PostApi Factory
 *
 * @author Matthias Pfefferle
 */
class PostApiFactory {

  /**
   * returns the matching PostApiClient
   *
   * @author Matthias Pfefferle
   * @param int $pCommunity
   * @param array $pParams
   * @return Object
   */
  static public function factory($pCommunity, $pParams = null) {
    // check if $pCommunity is the name or the id
    if (is_numeric($pCommunity)) {
      $lCommunityObject = CommunityTable::getInstance()->retrieveByPk($pCommunity);
      $lCommunity = $lCommunityObject->getCommunity();
    } else {
      $lCommunity = $pCommunity;
    }

    // return matching object
    switch ($lCommunity) {
      case "facebook":
        return new FacebookPostApiClient();
        break;
      case "twitter":
        return new TwitterPostApiClient();
        break;
      case "linkedin":
        return new LinkedinPostApiClient();
        break;
      /*case sfConfig::get('app_likewidget_google'):
        return new BuzzPostApiClient();
        break;*/
      default:
        return null;
    }
  }
}