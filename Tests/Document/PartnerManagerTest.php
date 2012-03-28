<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\PartnerBundle\Tests\Document;


use Doctrine\Bundle\MongoDBBundle\Mapping\Driver\XmlDriver;

use Vespolina\PartnerBundle\Model\Partner;
use Vespolina\PartnerBundle\Model\PartnerInterface;

use Doctrine\Bundle\MongoDBBundle\Tests\TestCase;

use Vespolina\PartnerBundle\Document\PartnerManager;
use Vespolina\PartnerBundle\Tests\PartnerTestCommon;
use Vespolina\PartnerBundle\Tests\Fixtures\Document\Partnerable;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class PartnerManagerTest extends TestCase
{
    /**
     * 
     * @var \Vespolina\PartnerBundle\Document\PartnerManager
     */
    protected $partnerMgr;
    
    /**
     * 
     * Enter description here ...
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    public function setup()
    {
        $this->dm = self::createTestDocumentManager();
        $path = realpath(__DIR__.'/../') . '/Resources/config/doctrine';
        $xmlDriver = new XmlDriver(array($path => 'Vespolina\PartnerBundle\Tests\Fixtures\Document'));
        $xmlDriver->setFileExtension('.mongodb.xml');
        $this->dm->getConfiguration()->setMetadataDriverImpl($xmlDriver);
        $this->partnerMgr = new PartnerManager(
            $this->dm,
            'Vespolina\PartnerBundle\Tests\Fixtures\Document\Partner',
            array(
                Partner::ROLE_CUSTOMER,
                Partner::ROLE_EMPLOYEE,
                Partner::ROLE_SUPPLIER,
            )
        );
    }

    public function tearDown()
    {
        $collections = $this->dm->getDocumentCollections();
        foreach ($collections as $collection) {
            $collection->drop();
        }
    }
    
    public function testCreatePartner()
    {
        $partner = $this->partnerMgr->createPartner();
        $partner->setPartnerId('TEST12345');
        $partner->addRole(Partner::ROLE_EMPLOYEE);
        
        $this->assertEquals(Partner::INDIVIDUAL, $partner->getType());
        $this->assertEquals($partner->getPartnerId(), 'TEST12345');
        $this->assertEquals(array(Partner::ROLE_CUSTOMER, Partner::ROLE_EMPLOYEE), $partner->getRoles());
    }
    
    public function testFindPartner()
    {
        $p = $this->partnerMgr->createPartner(Partner::ORGANISATION);
        $p->setPartnerId('PartnerTest002');
        $p->addRole(Partner::ROLE_EMPLOYEE);
        $this->dm->persist($p);
        $this->dm->flush();
        
        $partner = $this->partnerMgr->findOneByPartnerId('PartnerTest002');
        $this->assertTrue($partner instanceOf PartnerInterface);
        $this->assertEquals(Partner::ORGANISATION, $partner->getType());
        $this->assertEquals(array(Partner::ROLE_CUSTOMER, Partner::ROLE_EMPLOYEE), $partner->getRoles());
    }
}
