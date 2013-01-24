<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Partner\Manager;

use Vespolina\Entity\Partner\Partner;
use Vespolina\Entity\Partner\PartnerInterface;

interface PartnerManagerInterface
{
    /**
     * Returns a new instance of given partner type
     * @param string $role
     * @param string $type
     * @return \Vespolina\Entity\Partner\PartnerInterface
     */
    function createPartner($role = Partner::ROLE_CUSTOMER, $type = Partner::INDIVIDUAL);
    
    /**
     * Update and persist the partner
     *
     * @param \Vespolina\Entity\Partner\PartnerInterface $partner
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function updatePartner(PartnerInterface $partner, $andFlush = true);
    
    /**
     * Removes a partner
     * 
     * @param \Vespolina\Entity\Partner\PartnerInterface $partner
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function deletePartner(PartnerInterface $partner, $andFlush = true);
    
    /**
     * Creates and returns a new Partner Address
     * @return \Vespolina\Entity\Partner\AddressInterface
     */
    function createPartnerAddress();
    
    /**
     * Creates and returns a new Partner Contact
     * @return \Vespolina\Entity\Partner\ContactInterface
     */
    function createPartnerContact();
    
    /**
     * Creates and returns new Partner Personal Details
     * @return \Vespolina\Entity\Partner\PersonalDetailsInterface
     */
    function createPartnerPersonalDetails();
    
    /**
     * Creates and returns new Partner Organisation Details
     * @return \Vespolina\Entity\Partner\OrganisationDetailsInterface
     */
    function createPartnerOrganisationDetails();
    
    /**
     * Returns a single partner with given id
     * @param string $id
     * @return \Vespolina\Entity\Partner\PartnerInterface or null when no result found
     */
    function find($id);
    
    /**
     * Returns a single partner with given partnerId
     * @param string $partnerId
     * @return \Vespolina\Entity\Partner\PartnerInterface or null when no result found
     */
    function findOneByPartnerId($partnerId);
    
    /**
     * Returns all partners
     * 
     * @return
     */
    function findAll();
    
    /**
     * Returns all partners for given role
     * 
     * @param string $role
     * @return
     */
    function findAllByRole($role);
}