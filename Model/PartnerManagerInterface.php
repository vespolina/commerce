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
     * Creates and returns a new PartnerAddress
     * @return Vespolina\PartnerBundle\Model\AddressInterface
     */
    function createPartnerAddress();
    
    /**
     * Returns a single partner with given partnerId
     * @param string $partnerId
     * @return Vespolina\PartnerBundle\Model\PartnerInterface or null when no result found
     */
    function findOneByPartnerId($partnerId);
    
}