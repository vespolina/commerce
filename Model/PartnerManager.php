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
    protected $partnerAddressClass;
    protected $partnerRoles;
    
    public function __construct($partnerClass, $partnerAddressClass, array $partnerRoles)
    {
        $this->partnerClass            = $partnerClass;
        $this->partnerAddressClass     = $partnerAddressClass;
        $this->partnerRoles            = $partnerRoles;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createPartner($role = Partner::ROLE_CUSTOMER, $type = Partner::INDIVIDUAL)
    {
        /* @var $partner PartnerInterface */
        $partner = new $this->partnerClass;
        $partner->setType($type);
        
        if (!$this->isValidRole($role))
            throw new \Exception(sprintf("'%s' is not a valid role", $role));
            
        $partner->addRole($role);
        
        return $partner;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createPartnerAddress()
    {
        $address = new $this->partnerAddressClass;
        
        return $address;
    }
    
    /**
     * Returns if the given Role is valid.
     * @param string $role
     */
    public function isValidRole($role)
    {
        return in_array($role, $this->partnerRoles);
    }
    
}