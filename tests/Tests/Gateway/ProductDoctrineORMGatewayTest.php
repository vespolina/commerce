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
use Vespolina\Taxonomy\Gateway\TaxonomyMemoryGateway;

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
                __DIR__ . '/../../../vendor/vespolina/taxonomy/lib/Vespolina/Taxonomy/Mapping' => 'Vespolina\\Entity\\Taxonomy',
            ),
            '.orm.xml'
        );

        $drivers = new MappingDriverChain();
        $xmlDriver = new XmlDriver($locatorXml);

        $config->setMetadataDriverImpl($xmlDriver);
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setAutoGenerateProxyClasses(true);
        $em = EntityManager::create(array(
            'driver' => 'pdo_sqlite',
            'path' => 'database.sqlite'
        ), $config);

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = array(
            $em->getClassMetadata('Vespolina\Entity\Product\Product'),
        );

        try {
            $tool->createSchema($classes);
        } catch(\Exception $e) {}

        $this->productGateway = new ProductDoctrineORMGateway($em, 'Vespolina\Entity\Product\Product');
        $this->taxonomyGateway = new TaxonomyMemoryGateway('Vespolina\Entity\Taxonomy\TaxonomyNode');

    }
}
