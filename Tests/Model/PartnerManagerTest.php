<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\PartnerBundle\Tests\Model;

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
        $partner = $this->getManager()->createPartner(Partner::INDIVIDUAL);
        $this->assertTrue($partner instanceOf Partner);
        $this->assertEquals(Partner::INDIVIDUAL, $partner->getType());
        $this->assertContains(Partner::ROLE_CUSTOMER, $partner->getRoles());
    }
}