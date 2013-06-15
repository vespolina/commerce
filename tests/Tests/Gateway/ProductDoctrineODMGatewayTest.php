<?php
namespace Tests\Gateway;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;

use Vespolina\Product\Gateway\ProductDoctrineMongoDBGateway;

class ProductDoctrineODMGatewayTest extends ProductGatewayTestCommon
{
    protected function setUp()
    {
        $config = new \Doctrine\ODM\MongoDB\Configuration();
        $config->setHydratorDir(sys_get_temp_dir());
        $config->setHydratorNamespace('Hydrators');
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('Proxies');

        $locator = new SymfonyFileLocator(
            array(
                __DIR__ . '/../../../lib/Vespolina/Product/Mapping' => 'Vespolina\\Entity\\Product'
            ),
            '.mongodb.xml'
        );
        $config->setMetadataDriverImpl(new XmlDriver($locator));
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setAutoGenerateProxyClasses(true);
        $doctrineODM = \Doctrine\ODM\MongoDB\DocumentManager::create(null, $config);

        $this->gateway = new ProductDoctrineMongoDBGateway($doctrineODM, 'Vespolina\Entity\Product\Product');
    }
}
