<?php

/**
 * (c) 2013 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Tests\Brand\Gateway;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;
use Gedmo\Tree\TreeListener;
use Vespolina\Brand\Gateway\BrandDoctrineMongoDBGateway;

class BrandDoctrineODMGatewayTest extends BrandGatewayTestCommon
{
    protected function setUp()
    {
        $config = new Configuration();
        $config->setHydratorDir(sys_get_temp_dir());
        $config->setHydratorNamespace('Hydrators');
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('Proxies');

        $locatorXml = new SymfonyFileLocator(
            array(
                __DIR__ . '/../../../../../lib/Vespolina/Brand/Mapping' => 'Vespolina\\Entity\\Brand',
            ),
            '.mongodb.xml'
        );

        $xmlDriver = new XmlDriver($locatorXml);

        $config->setMetadataDriverImpl($xmlDriver);
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setAutoGenerateProxyClasses(true);
        $doctrineODM = DocumentManager::create(null, $config);
        $doctrineODM->getEventManager()->addEventSubscriber(new TreeListener());
        $this->brandGateway = new BrandDoctrineMongoDBGateway($doctrineODM, 'Vespolina\Entity\Brand\Brand');

        parent::setUp();
    }
}
