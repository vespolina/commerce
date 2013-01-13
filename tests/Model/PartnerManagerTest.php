<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\PartnerBundle\Tests\Model;

use Vespolina\PartnerBundle\Model\PersonalDetails;

use Vespolina\PartnerBundle\Model\AddressInterface;

use Vespolina\PartnerBundle\Model\IndividualPartner;
use Vespolina\PartnerBundle\Model\OrganisationPartner;
use Vespolina\PartnerBundle\Model\Partner;
use Vespolina\PartnerBundle\Model\Role;
use Vespolina\PartnerBundle\Model\PartnerManager;
use Vespolina\PartnerBundle\Tests\PartnerTestCommon;

/**
 * @author Willem-Jan Zijderveld <willemjan@beeldspraak.com>
 */
class PartnerManagerTest extends PartnerTestCommon
{
    public function testCreatePartner()
    {
        $partner = $this->getManager()->createPartner(Partner::ROLE_CUSTOMER, Partner::INDIVIDUAL);
        $this->assertTrue($partner instanceOf Partner);
        $this->assertEquals(Partner::INDIVIDUAL, $partner->getType());
        $this->assertContains(Partner::ROLE_CUSTOMER, $partner->getRoles());
    }
    
    public function testValidRoles()
    {
        $this->assertTrue($this->getManager()->isValidRole(Partner::ROLE_CUSTOMER));
        $this->assertFalse($this->getManager()->isValidRole('ROLE_XYZ'));
    }
    
    public function testCreateAddress()
    {
        $this->assertTrue($this->getManager()->createPartnerAddress() instanceOf AddressInterface);
    }
    
    public function testGenerateName()
    {
        $personalDetails = new PersonalDetails();
        $personalDetails->setFirstname('Willem-Jan');
        $personalDetails->setInitials('W');
        $personalDetails->setPrefix('the');
        $personalDetails->setLastname('Lastname');
        
        $this->assertEquals('W the Lastname', $this->getManager()->generatePartnerName($personalDetails));
    }
}