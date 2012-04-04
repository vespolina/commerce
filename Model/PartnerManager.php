<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\PartnerBundle\Model;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * PartnerManager - handles partner creation, updating, deletion, etc
 * 
 * @author Willem-Jan Zijderveld <willemjan@beeldspraak.com>
 */
abstract class PartnerManager implements PartnerManagerInterface
{
    /**
     * Configurable partner classes
     * @var string
     */
    protected $partnerClass,
              $partnerAddressClass,
              $partnerContactClass,
              $partnerPersonalDetailsClass;
    
    /**
     * Array with available partnerRoles
     * @var array $partnerRoles
     */
    protected $partnerRoles;
    
    /**
     * Constructor to setup the partner manager
     * 
     * @param array $classMapping - mapping for the partner class and his embedded classes
     * @param array $partnerRoles - array with available partner roles
     */
    public function __construct(array $classMapping, array $partnerRoles)
    {
        $missingClasses = array();
        foreach (array('', 'Address', 'Contact', 'PersonalDetails', 'OrganisationDetails') as $class) {
            $class = 'partner' . $class . 'Class';
            if (isset($classMapping[$class])) {
                
                if (!class_exists($classMapping[$class]))
                    throw new InvalidConfigurationException(sprintf("Class '%s' not found as '%s'", $classMapping[$class], $class));
                    
                $this->{$class} = $classMapping[$class];
                continue;
            } 
            $missingClasses[] = $class;
        }
        
        if (count($missingClasses)) {
            throw new InvalidConfigurationException(sprintf("The following partner classes are missing from configuration: %s", join(', ', $missingClasses)));
        }
        
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
        $address->setType(Address::INVOICE);
        
        return $address;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createPartnerContact()
    {
        $contact = new $this->partnerContactClass;
        
        return $contact;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createPartnerPersonalDetails()
    {
        $personalDetails = new $this->partnerPersonalDetailsClass;
        
        return $personalDetails;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createPartnerOrganisationDetails()
    {
        $organisationDetails = new $this->partnerOrganisationDetailsClass;
        
        return $organisationDetails;
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