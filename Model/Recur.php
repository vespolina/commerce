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
    protected $billingFrequency;
    protected $billingPeriod;
    protected $price;

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
    public function setBillingPeriod($billingPeriod)
    {
        $this->billingPeriod = $billingPeriod;
    }

    /**
     * @inheritdoc
     */
    public function getBillingPeriod()
    {
        return $this->billingPeriod;
    }

    /**
     * @inheritdoc
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return $this->price;
    }
}
