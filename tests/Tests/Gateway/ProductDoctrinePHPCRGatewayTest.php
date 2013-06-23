<?php
namespace Tests\Gateway;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Doctrine\Common\Annotations\AnnotationRegistry;


use Doctrine\ODM\PHPCR\Mapping\Driver\XmlDriver;
use Jackalope\Transport\DoctrineDBAL\RepositorySchema;
use Vespolina\Product\Gateway\ProductDoctrinePHPCRGateway;
use Vespolina\Taxonomy\Gateway\TaxonomyPHPCRGateway;

class ProductDoctrinePHPCRGatewayTest extends ProductGatewayTestCommon
{
    protected function setUp()
    {
        $params = array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'user'      => 'root',
            'password'  => '',
            'dbname'    => 'v_products_tests',
        );

        $workspace = 'default';
        $user = 'admin';
        $pass = 'admin';

        $dbConn = \Doctrine\DBAL\DriverManager::getConnection($params);
        $parameters = array('jackalope.doctrine_dbal_connection' => $dbConn);
        /**
        $schema = RepositorySchema::create();
        foreach ($schema->toSql($dbConn->getDatabasePlatform()) as $sql) {
            $dbConn->exec($sql);
        } */

        $repositoryFactory = new \Jackalope\RepositoryFactoryDoctrineDBAL();
        $repository = $repositoryFactory->getRepository($parameters);
        $credentials = new \PHPCR\SimpleCredentials(null, null);
        $session = $repository->login($credentials, $workspace);

        $locatorXml = new SymfonyFileLocator(
            array(
                __DIR__ . '/../../../lib/Vespolina/Product/Mapping' => 'Vespolina\\Entity\\Product',
                __DIR__ . '/../../../vendor/vespolina/pricing/lib/Vespolina/Pricing/Mapping' => 'Vespolina\\Entity\\Pricing',
                __DIR__ . '/../../../vendor/vespolina/taxonomy/lib/Vespolina/Taxonomy/Mapping' => 'Vespolina\\Entity\\Taxonomy',
            ),
            '.phpcr.xml'
        );
        $xmlDriver = new XmlDriver($locatorXml);

        $config = new \Doctrine\ODM\PHPCR\Configuration();
        $config->setMetadataDriverImpl($xmlDriver);
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setAutoGenerateProxyClasses(true);


        $documentManager = \Doctrine\ODM\PHPCR\DocumentManager::create($session, $config);
        $this->productGateway = new ProductDoctrinePHPCRGateway($documentManager, 'Vespolina\Entity\Product\Product');
        $this->taxonomyGateway = new TaxonomyPHPCRGateway($documentManager, 'Vespolina\Entity\Taxonomy\TaxonomyNode', '/');
        $this->taxonomyRootNode = $documentManager->find(null, '/');

    }

    public function testMatchProductById()
    {

    }
}
