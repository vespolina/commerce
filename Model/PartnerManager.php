<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\PartnerBundle\Model;

/**
 * PartnerManager - handles partner creation, updating, deletion, etc
 * 
 * @author Willem-Jan Zijderveld <willemjan@beeldspraak.com>
 */
abstract class PartnerManager implements PartnerManagerInterface
{
    protected $partnerClass;
    protected $partnerRoles;
    
    public function __construct($partnerClass, array $partnerRoles)
    {
        $this->partnerClass = $partnerClass;
        $this->partnerRoles = $partnerRoles;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createPartner($type = Partner::INDIVIDUAL)
    {
        /* @var $partner Partner */
        $partner = new $this->partnerClass;
        $partner->setType($type);
        $partner->addRole(Partner::ROLE_CUSTOMER);
        
        return $partner;
    }
}