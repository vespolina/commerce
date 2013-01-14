<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Vespolina\Entity\Partner\PersonalDetails;

use Vespolina\Entity\Partner\AddressInterface;

use Vespolina\Entity\Partner\IndividualPartner;
use Vespolina\Entity\Partner\OrganisationPartner;
use Vespolina\Entity\Partner\Partner;
use Vespolina\Entity\Partner\Role;
use Vespolina\Entity\Partner\PartnerManager;

/**
 * @author Willem-Jan Zijderveld <willemjan@beeldspraak.com>
 */
class PartnerManagerTest
{
    protected $partnerManager;

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

    protected function getManager()
    {
        if (!$this->partnerManager) {
            $this->partnerManager = $this->getMockForAbstractClass('Vespolina\Partner\Manager\PartnerManager',
                array(
                    array(
                        'partnerClass'                    => 'Vespolina\Entity\Partner\Partner',
                        'partnerAddressClass'             => 'Vespolina\Entity\Partner\Address',
                        'partnerContactClass'             => 'Vespolina\Entity\Partner\Contact',
                        'partnerPersonalDetailsClass'     => 'Vespolina\Entity\Partner\PersonalDetails',
                        'partnerOrganisationDetailsClass' => 'Vespolina\Entity\Partner\OrganisationDetails',
                    ),
                    array(Partner::ROLE_CUSTOMER)
                )
            );
        }

        return $this->partnerManager;
    }
}