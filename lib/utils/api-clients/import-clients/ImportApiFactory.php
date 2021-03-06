<?php
/**
 * PostApi Factory
 *
 * @author Matthias Pfefferle
 */
class ImportApiFactory {

  /**
   * returns the matching PostApiClient
   *
   * @author Matthias Pfefferle
   * @param mixed $pCommunity the community name or the community id
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
        return new FacebookImportClient();
        break;
      case "twitter":
        return new TwitterImportClient();
        break;
      case "linkedin":
        return new LinkedinImportClient();
        break;
      case "xing":
        return new XingImportClient();
        break;
      case "tumblr":
        return new TumblrImportClient();
        break;
      case "flattr":
        return new FlattrImportClient();
        break;
      default:
        return null;
    }
  }
}