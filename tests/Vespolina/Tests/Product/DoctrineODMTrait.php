<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Tests\Product;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;
use Gedmo\Tree\TreeListener;
use Vespolina\Brand\Gateway\BrandDoctrineMongoDBGateway;
use Vespolina\Product\Gateway\ProductDoctrineMongoDBGateway;
use Vespolina\Taxonomy\Gateway\TaxonomyDoctrineMongoDBGateway;

trait DoctrineODMTrait
{
    /** @var  \Vespolina\Brand\Gateway\BrandDoctrineMongoDBGateway */
    protected $brandGateway;
    /** @var  \Vespolina\Product\Gateway\ProductGatewayInterface */
    protected $productGateway;
    protected $taxonomyGateway;

    protected function dbSetUp()
    {
        $config = new \Doctrine\ODM\MongoDB\Configuration();
        $config->setHydratorDir(sys_get_temp_dir());
        $config->setHydratorNamespace('Hydrators');
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('Proxies');

        $locatorXml = new SymfonyFileLocator(
            array(
                __DIR__ . '/../../../../lib/Vespolina/Brand/Mapping' => 'Vespolina\\Entity\\Brand',
                __DIR__ . '/../../../../lib/Vespolina/Product/Mapping' => 'Vespolina\\Entity\\Product',
                __DIR__ . '/../../../../lib/Vespolina/Pricing/Mapping' => 'Vespolina\\Entity\\Pricing',
                __DIR__ . '/../../../../vendor/vespolina/taxonomy/lib/Vespolina/Taxonomy/Mapping' => 'Vespolina\\Entity\\Taxonomy',
            ),
            '.mongodb.xml'
        );

        $xmlDriver = new XmlDriver($locatorXml);

        $config->setMetadataDriverImpl($xmlDriver);
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setAutoGenerateProxyClasses(true);
        $doctrineODM = \Doctrine\ODM\MongoDB\DocumentManager::create(null, $config);
        $doctrineODM->getEventManager()->addEventSubscriber(new TreeListener());
        $this->brandGateway = new BrandDoctrineMongoDBGateway($doctrineODM, 'Vespolina\Entity\Brand\Brand');
        $this->productGateway = new ProductDoctrineMongoDBGateway($doctrineODM, 'Vespolina\Entity\Product\Product');
        $this->taxonomyGateway = new TaxonomyDoctrineMongoDBGateway($doctrineODM, 'Vespolina\Entity\Taxonomy\TaxonomyNode');
    }
}
