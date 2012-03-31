<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\PartnerBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\PartnerBundle\Model\Partner;
use Vespolina\PartnerBundle\Model\PartnerManager;

abstract class PartnerTestCommon extends WebTestCase
{
    static $kernel;
    
    /**
     * PartnerManager
     * @var Vespolina\PartnerBundle\Model\PartnerManager
     */
    protected $manager;
    
    public function getKernel(array $options = array())
    {
        if (!self::$kernel) {
            self::$kernel = $this->createKernel($options);
            self::$kernel->boot();
        }

        return self::$kernel;
    }
    
    public function getManager()
    {
        if (!$this->manager) {
            $this->manager = $this->getMockForAbstractClass('Vespolina\PartnerBundle\Model\PartnerManager', 
                array(
                	array(
                		'partnerClass'                    => 'Vespolina\PartnerBundle\Model\Partner', 
                		'partnerAddressClass'             => 'Vespolina\PartnerBundle\Model\Address',
                		'partnerContactClass'             => 'Vespolina\PartnerBundle\Model\Contact',
                		'partnerPersonalDetailsClass'     => 'Vespolina\PartnerBundle\Model\PersonalDetails',
                		'partnerOrganisationDetailsClass' => 'Vespolina\PartnerBundle\Model\OrganisationDetails',
                	), 
                    array(Partner::ROLE_CUSTOMER)
                )
            );
        }
        
        return $this->manager;
    }
}