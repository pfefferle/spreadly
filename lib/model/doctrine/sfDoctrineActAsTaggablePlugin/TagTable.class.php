<?php


class TagTable extends PluginTagTable
{

    public static function getInstance()
    {
        return Doctrine_Core::getTable('Tag');
    }

    /**
     * @todo distinct auf taggabel-id...
     * Enter description here ...
     * @param string $pString
     */
    public static function getAllTagsByString($pString) {

      /*$lQuery = Doctrine_Query::create()
      ->select('DISTINCT t.name')
      ->from('Tag t')
      ->where('t.name LIKE ?', '%'.$pString.'%');*/

    $lQuery = Doctrine_Query::create()
      ->select('DISTINCT tag.name')
      ->from('Tag tag')
      ->leftJoin('tag.Tagging tagging')
      ->where('tag.name LIKE ?', '%'.$pString.'%')
      ->andWhere('tagging.taggable_model = "User" OR tagging.taggable_model = "DomainProfile"');



      return $lQuery->fetchArray();
    }
}