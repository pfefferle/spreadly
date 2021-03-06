<?php

/**
 * DomainProfile
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    yiid_stats
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class DomainProfile extends BaseDomainProfile
{
  const ERROR_INVALID_URL = 'Url not available!';
  const ERROR_NO_MICROID = 'No microid found!';
  const ERROR_INVALID_MICROID = 'MicroId not valid!';
  const ERROR_VERIFICATION = 'Could not verify domain!';

  public function generateVerificationToken(sfGuardUser $user) {
    $this->setVerificationToken(MicroId::generate('mailto:'.$user->getEmailAddress(), $this->getDomain()));
  }

  public function getDomain() {
    return $this->getProtocol().'://'.$this->getUrl();
  }

  public function isPending() {
    return $this->getState()===DomainProfileTable::STATE_PENDING;
  }

  public function isVerified() {
    return $this->getState()===DomainProfileTable::STATE_VERIFIED;
  }

  public function preSave($obj) {
    $this->generateVerificationToken($this->getSfGuardUser());
  }

  public function verify() {
    $count = $this->getVerificationCount();
    $this->setVerificationCount($count+1);

    $microids = $this->getMicroIdsFromDomain();
    $verified = DomainProfile::ERROR_VERIFICATION;
    if(is_array($microids)) {
      if(count($microids) > 0) {
        foreach ($microids as $microid) {
          if($microid === $this->getVerificationToken()) {
            $verified = true;
          }
        }
        if($verified===true) {
          $this->setState('verified');
        } else {
          $verified = DomainProfile::ERROR_INVALID_MICROID;
        }
      } else {
        $verified = DomainProfile::ERROR_NO_MICROID;
      }
    } else {
      $verified = DomainProfile::ERROR_INVALID_URL;
    }

    $this->save();

    if($this->getState() == DomainProfileTable::STATE_VERIFIED) {
      DomainProfileTable::getInstance()->deleteUnverified();
    }

    return $verified;
  }

  private function getMicroidsFromDomain() {
    $url = $this->getDomain();
    $protocol = $this->getProtocol();
    if(UrlUtils::checkUrlWithCurl($url, true)) {
      return MicroID::parseString(UrlUtils::getUrlContent($url));
    } else {
      return null;
    }
  }

  public function hasSubscriber() {
    if (DomainSubscriptionsTable::getInstance()->findOneBy("domain_profile_id", $this->getId())) {
      return true;
    }
    return false;
  }

  public function getUniqueId() {
    return "tag:spreadly.com,".date('Y', strtotime($this->getCreatedAt())).":/domain/".$this->getId();
  }

  public function __toString() {
    return $this->getId().': '. $this->getUrl();
  }

  /**
   * get the full price a domain profile earned
   *
   * @return array
   */
  public function getCommissionSum() {
    $sum = Doctrine_Query::create()
        ->limit(0)
        ->select('sum(price)')
        ->from('commission')
        ->where('domain_profile_id = ?', $this->getId())
        ->fetchArray();

    return floatval(round($sum[0]['sum'], 2, PHP_ROUND_HALF_UP));
  }

  /**
   * get a monthly stats of the money a domain profile earned
   *
   * @return array
   */
  public function getCommissionStats() {
    $stats = Doctrine_Query::create()
        ->select('sum(price), month( created_at ) as month,  year( created_at ) as year, created_at')
        ->from('commission')
        ->where('domain_profile_id = ?', $this->getId())
        ->groupBy('month, year')
        ->orderBy('created_at ASC')
        ->fetchArray();

    return $stats;
  }

  /**
   * get the full price a domain profile earned
   *
   * @return array
   */
  public function getCommissionSumOfLast30Days() {
    $sum = Doctrine_Query::create()
        ->limit(0)
        ->select('sum(price)')
        ->from('commission')
        ->where('domain_profile_id = ? AND created_at >= ? AND created_at <= ?', array($this->getId(), date("c", strtotime("now - 30 days")), date("c", strtotime("now"))))
        ->fetchArray();

    return floatval(round($sum[0]['sum'], 2, PHP_ROUND_HALF_UP));
  }
}