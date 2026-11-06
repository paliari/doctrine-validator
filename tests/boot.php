<?php
use Doctrine\DBAL\DriverManager;
use Doctrine\Deprecations\Deprecation;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Paliari\Doctrine\ModelValidatorEventSubscriber;

include __DIR__ . '/../vendor/autoload.php';

Deprecation::enableWithTriggerError();

foreach (glob(__DIR__ . '/models/*.php') as $file) {
    include_once "$file";
}
include_once __DIR__ . '/EM.php';

function createEntityManager(): EntityManager
{
    $config = new Configuration();
    $config->setMetadataDriverImpl(new AttributeDriver([__DIR__ . '/models'], true));
    if (PHP_VERSION_ID >= 80400 && method_exists($config, 'enableNativeLazyObjects')) {
        $config->enableNativeLazyObjects(true);
    } else {
        if (method_exists($config, 'setLazyGhostObjectEnabled')) {
            $config->setLazyGhostObjectEnabled(true);
        }
        $config->setProxyDir(__DIR__ . '/../tmp/proxies');
        $config->setProxyNamespace('Proxies');
        $config->setAutoGenerateProxyClasses(true);
    }
    $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $config);
    $em = new EntityManager($connection, $config);
    $em->getEventManager()->addEventSubscriber(new ModelValidatorEventSubscriber());

    return $em;
}

EM::setEm(createEntityManager());
