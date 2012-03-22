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
     */
    function createPartner($partnerType);
    
    /**
     * Returns a single partner with given partnerId
     * @param string $partnerId
     */
    function findByPartnerId($partnerId);
}