<?php
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Paliari\Doctrine\ModelValidatorEventSubscriber;

include __DIR__ . '/../vendor/autoload.php';

foreach (glob(__DIR__ . '/models/*.php') as $file) {
    include_once "$file";
}
include_once __DIR__ . '/EM.php';
$params = [
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/foo.db',
    'memory' => true,
    'charset' => 'UTF8',
    'driverOptions' => ['charset' => 'UTF8'],
];
$config = new Configuration();
$driverImpl = $config->newDefaultAnnotationDriver(__DIR__ . '/models');
$config->setMetadataDriverImpl($driverImpl);
$config->setProxyDir(__DIR__ . '/../tmp/proxies');
$config->setProxyNamespace('Proxies');
$config->setAutoGenerateProxyClasses(true);
$em = EntityManager::create($params, $config);
EM::setEm($em);
$eventManager = $em->getEventManager();
$eventManager->addEventSubscriber(new ModelValidatorEventSubscriber());
