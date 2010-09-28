<?php
require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('platform', 'batch', false);
sfContext::createInstance($configuration);
$logger = sfContext::getInstance()->getLogger();

// Initialize database manager.
$dbManager = new sfDatabaseManager($configuration);
$dbManager->loadConfiguration();


/**
 *  we need all user id's first
 **/


$service = AmazonSQSUtils::initSqsService();
$dummy = $service->getQueueAttributes('ImportContacts');
var_dump($dummy);
$dummy = $service->getQueueAttributes('SocialObjectParser');
var_dump($dummy);
$dummy = $service->getQueueAttributes('SocialObjectParser-dev');
var_dump($dummy);
$dummy = $service->getQueueAttributes('SocialObjectParser-local');
var_dump($dummy);