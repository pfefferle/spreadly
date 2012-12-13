<?php
namespace Documents;

use \MongoDate,
    \MongoManager;

/**
 * Base Class to represent Advertisements
 *
 * @author Matthias Pfefferle
 *
 * @Document(collection="advertisement", repositoryClass="Repositories\AdvertisementRepository")
 * @HasLifecycleCallbacks
 * @InheritanceType("SINGLE_COLLECTION")
 */
class Advertisement extends BaseDocument {

  /** 
   * @Id
   */
  protected $id;

  /**
   * The Domain Profile ID
   * 
   * @Int
   */
  protected $domain_profile_id;
  
  /**
   * A list of domains where the ad should be displayed
   * at
   *
   * @Hash
   */
  protected $domains;
  
  /**
   * The user_id of the ad-"owner"
   * (needed for the stats)
   *
   * @Int
   */
  protected $ad_owner_id;
  
  /**
   * The url to an advertisement image
   *
   * @String
   */
  protected $ad_image;
  
  /**
   * A link to the advertisement page
   *
   * @String
   */
  protected $ad_link;
  
  /**
   * Any HTML-snippet
   *
   * @String
   */
  protected $ad_code;
  
  /**
   * The width of the ad layer
   *
   * @Int
   */
  protected $ad_width;
  
  /**
   * The height of the ad layer
   *
   * @Int
   */
  protected $ad_height;
  
  /**
   * Last changes to the object
   *
   * @Int
   */
  protected $updated_at;
  
  /**
   * The date where the advertisement
   * should show up 
   *
   * @Int
   */
  protected $starting_at;
  
  /**
   * A nicer way to save the object
   */
  public function save() {
    $dm = MongoManager::getDM();
    $dm->persist($this);
    $dm->flush();
  }
}
