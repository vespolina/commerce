<?php
namespace Tests\Gateway;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;

use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;
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

        $locatorXml = new SymfonyFileLocator(
            array(
                __DIR__ . '/../../../lib/Vespolina/Product/Mapping' => 'Vespolina\\Entity\\Product',
                __DIR__ . '/../../../vendor/vespolina/pricing/lib/Vespolina/Pricing/Mapping' => 'Vespolina\\Entity\\Pricing',
        ),
            '.mongodb.xml'
        );
        $locatorYaml = new SymfonyFileLocator(
            array(
                __DIR__ . '/../../../vendor/vespolina/taxonomy/lib/Vespolina/Taxonomy/Mapping' => 'Vespolina\\Entity\\Taxonomy',
            ),
            '.mongodb.yml'
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
        $doctrineODM = \Doctrine\ODM\MongoDB\DocumentManager::create(null, $config);

        $this->gateway = new ProductDoctrineMongoDBGateway($doctrineODM, 'Vespolina\Entity\Product\Product');
    }

    public function testMatchProductById()
    {

    }
}
