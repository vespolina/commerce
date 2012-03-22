<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\PartnerBundle\Model;

interface PrimaryContactInterface
{
    /**
     * Sets the contacts phone number
     * 
     * @param string $phone
     */
    function setPhone($phone);
    
    /**
     * Primary phone number
     * 
     * @return string
     */
    function getPhone();
    
    /**
     * Sets the contacts emailaddress
     * @param string $email
     */
    function setEmail($email);
    
    /**
     * Primary email address for communication
     * 
     * @return string
     */
    function getEmail();
}