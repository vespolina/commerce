<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\PartnerBundle\Model;

interface PartnerManagerInterface
{
    /**
     * Returns a new instance of given partner type
     * @param string $partnerType
     * @return Vespolina\PartnerBundle\Model\PartnerInterface
     */
    function createPartner($role, $partnerType);
    
    /**
     * Update and persist the partner
     *
     * @param Vespolina\PartnerBundle\Model\PartnerInterface $partner
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function updatePartner(PartnerInterface $partner, $andFlush = true);
    
    /**
     * Removes a partner
     * 
     * @param Vespolina\PartnerBundle\Model\PartnerInterface $partner
     * @param Boolean $andFlush Wheter to flush the changes (default true)
     */
    function deletePartner(PartnerInterface $partner, $andFlush = true);
    
    /**
     * Creates and returns a new Partner Address
     * @return Vespolina\PartnerBundle\Model\AddressInterface
     */
    function createPartnerAddress();
    
    /**
     * Creates and returns a new Partner Contact
     * @return Vespolina\PartnerBundle\Model\ContactInterface
     */
    function createPartnerContact();
    
    /**
     * Creates and returns new Partner Personal Details
     * @return Vespolina\PartnerBundle\Model\PersonalDetailsInterface
     */
    function createPartnerPersonalDetails();
    
    /**
     * Creates and returns new Partner Organisation Details
     * @return Vespolina\PartnerBundle\Model\OrganisationDetailsInterface
     */
    function createPartnerOrganisationDetails();
    
    /**
     * Returns a single partner with given id
     * @param string $id
     * @return Vespolina\PartnerBundle\Model\PartnerInterface or null when no result found
     */
    function find($id);
    
    /**
     * Returns a single partner with given partnerId
     * @param string $partnerId
     * @return Vespolina\PartnerBundle\Model\PartnerInterface or null when no result found
     */
    function findOneByPartnerId($partnerId);
    
    /**
     * Returns all partners
     * 
     * @return Doctrine\MongoDB\Collection
     */
    function findAll();
    
    /**
     * Returns all partners for given role
     * 
     * @param string $role
     * @return Doctrine\MongoDB\Collection
     */
    function findAllByRole($role);
    
}