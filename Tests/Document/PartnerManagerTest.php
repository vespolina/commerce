<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\PartnerBundle\Tests\Document;


use Vespolina\PartnerBundle\Model\Address;

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
        $p = $this->partnerMgr->createPartner(Partner::ROLE_CUSTOMER, Partner::ORGANISATION);
        $p->setPartnerId('PartnerTest002');
        $p->addRole(Partner::ROLE_EMPLOYEE);
        $this->dm->persist($p);
        $this->dm->flush();
        
        $partner = $this->partnerMgr->findOneByPartnerId('PartnerTest002');
        $this->assertTrue($partner instanceOf PartnerInterface);
        $this->assertEquals(Partner::ORGANISATION, $partner->getType());
        $this->assertEquals(array(Partner::ROLE_CUSTOMER, Partner::ROLE_EMPLOYEE), $partner->getRoles());
    }
    
    public function testUpdatePartner()
    {
        $partner = $this->partnerMgr->createPartner();
        $partner->setPartnerId('testUpdatePartner');
        
        $this->partnerMgr->updatePartner($partner);
        
        $this->assertTrue($this->partnerMgr->findOneByPartnerId('testUpdatePartner') instanceOf PartnerInterface);   
    }
    
    public function testFullFeateredPartner()
    {
        $partner = $this->partnerMgr->createPartner();
        $partner->setPartnerId('PartnerId003');
        $partner->setName('PartnerName');
        $partner->setPartnerSince(new \DateTime('2012-03-01'));
        $partner->setCurrency('EUR');
        $partner->setLanguage('nl_NL');
        $partner->setPaymentTerms('PartnerPaysWhenHeLikes');
        
        $address = $this->partnerMgr->createPartnerAddress();
        $address->setType(Address::INVOICE);
        $address->setStreet('AddressStreet');
        $address->setNumber(42);
        $address->setNumberSuffix('A');
        $address->setZipcode('1234AA');
        $address->setCity('Rotterdam');
        $address->setState('Zuid-Holland');
        $address->setCountry('The Netherlands');
        
        $partner->addAddress($address);
        
        $contact = $this->partnerMgr->createPartnerContact();
        $contact->setName('PartnerContactName');
        $contact->setEmail('contact@example.org');
        $contact->setPhone('0810-555-12345');
        
        $partner->setPrimaryContact($contact);
        
        $personalDetails = $this->partnerMgr->createPartnerPersonalDetails();
        $personalDetails->setNationalIdentificationNumber('1234567890');
        
        $partner->setPersonalDetails($personalDetails);
        
        $this->partnerMgr->updatePartner($partner);
        
        unset($partner);
        
        $partner = $this->partnerMgr->findOneByPartnerId('PartnerId003');
        $this->assertTrue($partner instanceOf PartnerInterface);
        
        // details
        $this->assertEquals('PartnerName', $partner->getName());
        $this->assertEquals(new \DateTime('2012-03-01'), $partner->getPartnerSince());
        $this->assertEquals('EUR', $partner->getCurrency());
        $this->assertEquals('nl_NL', $partner->getLanguage());
        $this->assertEquals('PartnerPaysWhenHeLikes', $partner->getPaymentTerms());
        
        // address
        $addresses = $partner->getAddresses();
        $this->assertEquals(1, count($addresses));
        $this->assertEquals('AddressStreet', $addresses[0]->getStreet());
        $this->assertSame(42, $addresses[0]->getNumber());
        $this->assertEquals('A', $addresses[0]->getNumberSuffix());
        $this->assertEquals('1234AA', $addresses[0]->getZipcode());
        $this->assertEquals('Rotterdam', $addresses[0]->getCity());
        $this->assertEquals('Zuid-Holland', $addresses[0]->getState());
        $this->assertEquals('The Netherlands', $addresses[0]->getCountry());
        
        // primary contact
        $contact = $partner->getPrimaryContact();
        $this->assertEquals('PartnerContactName', $contact->getName());
        $this->assertEquals('contact@example.org', $contact->getEmail());
        $this->assertEquals('0810-555-12345', $contact->getPhone());
        
        // personal details
        $details = $partner->getPersonalDetails();
        $this->assertEquals('1234567890', $details->getNationalIdentificationNumber());
    }

    public function testCustomField()
    {
        $partner = $this->partnerMgr->createPartner();
        $partner->setDateOfBirth(new \DateTime('1987-02-12'));
        
        $this->assertEquals(new \DateTime('1987-02-12'), $partner->getDateOfBirth());
    }

    public function setup()
    {
        $this->dm = self::createTestDocumentManager();
        $xmlDriver = new XmlDriver(array(realpath(__DIR__.'/../') . '/Resources/config/doctrine' => 'Vespolina\PartnerBundle\Tests\Fixtures\Document'));
        $xmlDriver->setFileExtension('.mongodb.xml');
        $this->dm->getConfiguration()->setMetadataDriverImpl($xmlDriver);
        $this->partnerMgr = new PartnerManager(
            $this->dm,
            array(
        		'partnerClass'                    => 'Vespolina\PartnerBundle\Tests\Fixtures\Document\Partner', 
        		'partnerAddressClass'             => 'Vespolina\PartnerBundle\Document\Address',
        		'partnerContactClass'             => 'Vespolina\PartnerBundle\Document\Contact',
        		'partnerPersonalDetailsClass'     => 'Vespolina\PartnerBundle\Document\PersonalDetails',
        		'partnerOrganisationDetailsClass' => 'Vespolina\PartnerBundle\Document\OrganisationDetails',
        	),
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
}
