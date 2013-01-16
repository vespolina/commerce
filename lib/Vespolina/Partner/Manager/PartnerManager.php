<?php
/**
 * (c) 2012-2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Partner\Manager;

use Vespolina\Entity\Partner\PersonalDetails;
use Vespolina\Partner\Gateway\PartnerGateway;
use Vespolina\Entity\Partner\Address;
use Vespolina\Entity\Partner\Partner;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Exception\InvalidConfigurationException;

/**
 * PartnerManager - handles partner creation, updating, deletion, etc
 * 
 * @author Willem-Jan Zijderveld <willemjan@beeldspraak.com>
 */
class PartnerManager implements PartnerManagerInterface
{
    /**
     * Configurable partner classes
     * @var string
     */
    protected $gateway;
    protected $partnerAddressClass;
    protected $partnerClass;
    protected $partnerContactClass;
    protected $partnerPersonalDetailsClass;
    
    /**
     * Array with available partnerRoles
     * @var array $partnerRoles
     */
    protected $partnerRoles;
    
    /**
     * Constructor to setup the partner manager
     *
     * @param \Vespolina\Partner\Gateway\PartnerGateway $gateway
     * @param array $classMapping - mapping for the partner class and his embedded classes
     * @param array $partnerRoles - array with available partner roles
     */
    public function __construct(PartnerGateway $gateway, array $classMapping, array $partnerRoles)
    {
        $this->gateway = $gateway;
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
        
        $this->partnerRoles = $partnerRoles;
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
        
        switch ($type) {
            case Partner::INDIVIDUAL:
                $partner->setPersonalDetails($this->createPartnerPersonalDetails());
                break;
            case Partner::ORGANISATION:
                $partner->setOrganisationDetails($this->createPartnerOrganisationDetails());
                break;
        }
        
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
     * {@inheritdoc}
     */
    public function deletePartner(PartnerInterface $partner, $andFlush = true)
    {
        $this->gateway->deletePartner($partner);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $query = $this->gateway->createQuery('Select');
        $query->filterEqual('id', $id);

        return $this->gateway->findPartner($query);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByPartnerId($partnerId)
    {
        $query = $this->gateway->createQuery('Select');
        $query->filterEqual('partnerId', $partnerId);

        return $this->gateway->findPartner($query);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $query = $this->gateway->createQuery('Select');

        return $this->gateway->findPartners($query);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByRole($role)
    {
        $query = $this->gateway->createQuery('Select');
        $query->filterEqual('roles', $role);

        return $this->gateway->findPartners($query);
    }

    /**
     * Generates and returns the name of the partner
     * @return string
     */
    public function generatePartnerName(PersonalDetails $personalDetails)
    {
        $parts = array();
        foreach (array('initials', 'prefix', 'lastname') as $part) {
            if ('' !== ($value = (string)$personalDetails->{'get'.ucfirst($part)}()))
                $parts[] = $value;
        }

        return join(' ', $parts);
    }

    /**
     * Returns if the given Role is valid.
     * @param string $role
     */
    public function isValidRole($role)
    {
        return in_array($role, $this->partnerRoles);
    }

    /**
     * {@inheritdoc}
     */
    public function updatePartner(PartnerInterface $partner, $andFlush = true)
    {
        $this->gateway->updatePartner($partner);
    }
}