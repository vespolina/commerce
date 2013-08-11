<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Handler;

use Vespolina\Order\Handler\AbstractOrderHandler;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Order\OrderInterface;

/**
 * DefaultHandler for the order
 */
class DefaultOrderHandler extends  AbstractOrderHandler
{
    protected $fulfillmentPricingEnabled;
    protected $taxPricingEnabled;

    public function __construct()
    {
        $this->fulfillmentPricingEnabled = true;
        $this->taxPricingEnabled = true;
    }

    public function determineOrderItemPrices(ItemInterface $orderItem, $pricingContext)
    {
        $pricing = $orderItem->getProduct()->getPricing();
        $pricingSet = $orderItem->getPricing();
        $unitNet = $pricing->get('unitPriceTotal');
        $upChargeNet = 0;

        //Add additional upcharges for a chosen product option
        $upChargeNet = $this->determineOrderItemUpCharge($orderItem, $pricingContext);

        //Calculate fulfillment costs (eg. shipping, packaging cost)
        if ($this->fulfillmentPricingEnabled) {
            //$this->determineOrderItemFulfillmentPrices($orderItem, $pricingContext);
        }

        $totalNet = ( $orderItem->getQuantity() * $unitNet ) + $upChargeNet;
        $pricingSet->set('upChargeNet', $upChargeNet);
        $pricingSet->set('totalNet', $totalNet);

        //Determine item level taxes
        $taxationEnabled = $orderItem->getOrder()->getAttribute('taxation_enabled');

        if ($taxationEnabled) {

            $this->determineOrderItemTaxes(
                    $orderItem,
                    array('totalNet' => $totalNet),
                    $pricingSet,
                    $pricingContext);
        }

        $pricingSet->set('totalGross', $pricingContext['totalNet'] + $pricingContext['totalTax']);
    }

    public function getTypes()
    {
        return 'default';
    }

    protected function determineOrderItemUpCharge(ItemInterface $orderItem, $pricingContext)
    {
        $upCharge = 0;

        foreach($orderItem->getOptions() as $type => $value) {

            if ($productOption = $orderItem->getProduct()->getOptionSet(array($type => $value))) {
                $upCharge += $productOption->getUpcharge();
            }
        }

        return $upCharge;
    }

    protected function determineOrderItemTaxes(ItemInterface $orderItem, array $pricesToBeTaxed, $orderItemPricingSet, $pricingContext)
    {

        $rate = 0;
        $taxes = array();
        $totalTax = 0;

        //We currently assume that all order items use the default tax zone and associated tax rate
        $taxZone = $pricingContext->get('taxZone');

        if (null != $taxZone) {
            $rate = $taxZone->getDefaultRate();
        }

        //Each price which should be taxed is aggregated into one value, for instance shipment tax + sales tax
        //This is especially true for flat rate taxes, but insufficient for mixed tax rates
        foreach($pricesToBeTaxed as $name => $value) {

            $taxValue = $rate * $value / 100;
            $totalTax += $taxValue;
        }
        $orderItemPricingSet->set('totalTax', $totalTax);
  }

    protected function determineOrderFulfillmentPrices(OrderInterface $order, $pricingContext)
    {
        //Additional fulfillment to be applied not related to order item taxes
        // eg. fixed fulfillment fee
    }

    protected function sumItemPrices(ItemInterface $orderItem, $pricingContext)
    {
        return null;
        //$pricingContext['total'] += $orderItem->getPrice('total');
    }
}
