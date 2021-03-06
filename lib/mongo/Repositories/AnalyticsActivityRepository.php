<?php
namespace Repositories;

use \MongoDate,
    Documents\ActivityStats,
    Documents\ActivityUrlStats,
    Documents\Stats;

class AnalyticsActivityRepository extends StatsRepository
{
  public function groupByUsers($fromDay, $toDay) {
    return $this->groupBy(array('u_id' => true), $fromDay, $toDay);
  }

  public function groupByHosts($fromDay, $toDay) {
    return $this->groupBy(array('host' => true), $fromDay, $toDay);
  }

  public function groupByUrls($fromDay, $toDay) {
    return $this->groupBy(array('url' => true), $fromDay, $toDay);
  }

  private function groupBy($groupBy, $fromDay, $toDay) {
    $res = $this->createQueryBuilder()
      ->group($groupBy, array('count' => 0))
      ->reduce('function (obj, prev) { prev.count++; }')
      ->field("date")->gte(new MongoDate($fromDay))
      ->field("date")->lte(new MongoDate($toDay))
      ->getQuery()
      ->execute();
    $arr = $res['retval'];
    usort($arr, function($a, $b) {
      return $b['count'] - $a['count'];
      }
    );
    return $arr;
  }

  public function getOverallClickbacks($user_id) {
    $res = $this->createQueryBuilder()
      ->group(array("cb"), array('count' => 0))
      ->reduce('function (obj, prev) { prev.count+=obj.cb; }')
      ->field("u_id")->equals(intval($user_id))
      ->getQuery()
      ->execute();
    $arr = $res['retval'];

    return @$arr[0]['count'];
  }
  
  /**
   * returns the latest activities on a specific host
   *
   * @author Matthias Pfefferle
   * @param string $host
   * @param int $limit default = 10
   * @return array
   */
  public function findLatestByHost($host, $limit = 10) {
    $results = $this->createQueryBuilder()
                    ->hydrate(true)
                    ->limit($limit)
                    ->field("host")->equals($host)
                    ->sort("date", "DESC")
                    ->getQuery()->execute();

    return $results;
  }
}