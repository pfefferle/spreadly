<?php
// required because its called like a batch outside of the symfony context
require_once(dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php');
require_once(dirname(__FILE__).'/../../vendor/OpenGraph/OpenGraph.php');

/**
 * a parser for social objects
 *
 * @author Matthias Pfefferle
 */
class SocialObjectParser {

  /**
   * handles the parsing of a new social-object
   * currently parsed: opengraph and metatags
   *
   * @param string $pUrl
   * @return array $pArray
   */
  public static function fetch($pUrl, $pYiidMeta = null) {
    $pUrl = urldecode($pUrl);

    try {
      //get the html as string
      $lHtml = UrlUtils::getUrlContent($pUrl, 'GET');

      // boost performance and use alreade the header
      $lHeader = substr( $lHtml, 0, stripos( $lHtml, '</head>' ) );

      if (!$pYiidMeta) {
        $pYiidMeta = new YiidMeta();
      }

      if (preg_match('~http://opengraphprotocol.org/schema/~i', $lHeader)  && !$pYiidMeta->isComplete()) {
        //get the opengraph-tags
        $lOpenGraph = OpenGraph::parse($lHeader);
        $pYiidMeta->fromOpenGraph($lOpenGraph);
      }

      if ((preg_match('~application/(xml|json)\+oembed"~i', $lHeader)) && !$pYiidMeta->isComplete()) {
        $lOEmbed = OEmbedParser::fetchByCode($lHeader);
        $pYiidMeta->fromOembed($lOEmbed);
      }

      if (!$pYiidMeta->isComplete()) {
        $lMeta = MetaTagParser::getKeys($lHtml, $pUrl);
        $pYiidMeta->fromMeta($lMeta);
        /*
        if (!$lYiidMeta->getImages()) {
          foreach (ImageParser::fetch($lHtml, $pUrl) as $images) {
            $imgs[] = $images['image'];
          }

          $lYiidMeta->setImages($imgs);
        }
        */
      }

      return $pYiidMeta;
    } catch (Exception $e) {}
  }
}