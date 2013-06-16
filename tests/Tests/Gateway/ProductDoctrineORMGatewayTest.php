<?php
namespace Tests\Gateway;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

use Vespolina\Product\Gateway\ProductDoctrineORMGateway;

class ProductDoctrineORMGatewayTest extends ProductGatewayTestCommon
{
    protected function setUp()
    {
        $config = new Configuration();

        //$config->setHydratorDir(sys_get_temp_dir());
        //$config->setHydratorNamespace('Hydrators');
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('Proxies');

        $locatorXml = new SymfonyFileLocator(
            array(
                __DIR__ . '/../../../lib/Vespolina/Product/Mapping' => 'Vespolina\\Entity\\Product',
                __DIR__ . '/../../../vendor/vespolina/pricing/lib/Vespolina/Pricing/Mapping' => 'Vespolina\\Entity\\Pricing',
            ),
            '.orm.xml'
        );
        $locatorYaml = new SymfonyFileLocator(
            array(
                __DIR__ . '/../../../vendor/vespolina/taxonomy/lib/Vespolina/Taxonomy/Mapping' => 'Vespolina\\Entity\\Taxonomy',
            ),
            '.orm.yml'
        );
        $drivers = new MappingDriverChain();
        $xmlDriver = new XmlDriver($locatorXml);
        $ymlDriver = new YamlDriver($locatorYaml);

        $drivers->addDriver($xmlDriver, 'Vespolina\\Entity\\Product');
        $drivers->addDriver($xmlDriver, 'Vespolina\\Entity\\Pricing');
        $drivers->addDriver($ymlDriver, 'Vespolina\\Entity\\Taxonomy');

        $config->setMetadataDriverImpl($drivers);
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setAutoGenerateProxyClasses(true);
        $doctrineORM = EntityManager::create(array(
            'driver' => 'pdo_sqlite',
            'path' => 'database.sqlite'
        ), $config);

        $this->gateway = new ProductDoctrineORMGateway($doctrineORM, 'Vespolina\Entity\Product\Product');

    }
}
