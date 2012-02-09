<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface RecurInterface
{
    /**
     * Set the frequency of the billing period
     *
     * @param integer $billingFrequency
     */
    function setBillingFrequency($billingFrequency);

    /**
     * Return the frequency of the billing period
     *
     * @return integer
     */
    function getBillingFrequency();

    /**
     * Set the billing period, typically set to day, week, month, year
     *
     * @param string $billingInterval
     */
    function setBillingInterval($billingInterval);

    /**
     * Return the billing peroid
     *
     * @return string
     */
    function getBillingInterval();

    /**
     * Set the monetary amount of this recurring product
     *
     * @param $amount
     */
    function setAmount($amount);

    /**
     * Return the monetary amount of this recurring product
     *
     * @return $amount
     */
    function getAmount();
}
