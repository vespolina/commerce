<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\RecurInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class Recur implements RecurInterface
{
    protected $amount;
    protected $billingFrequency;
    protected $billingInterval;

    /**
     * @inheritdoc
     */
    public function setBillingFrequency($billingFrequency)
    {
        $this->billingFrequency = $billingFrequency;
    }

    /**
     * @inheritdoc
     */
    public function getBillingFrequency()
    {
        return $this->billingFrequency;
    }

    /**
     * @inheritdoc
     */
    public function setBillingInterval($billingInterval)
    {
        $this->billingInterval = $billingInterval;
    }

    /**
     * @inheritdoc
     */
    public function getBillingInterval()
    {
        return $this->billingInterval;
    }

    /**
     * @inheritdoc
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
