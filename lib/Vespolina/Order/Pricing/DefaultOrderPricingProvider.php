<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Pricing;

use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Pricing\PricingContext;
use Vespolina\Entity\Pricing\PricingContextInterface;
use Vespolina\Entity\ProductInterface;
use Vespolina\Order\Handler\OrderHandlerInterface;
use Vespolina\Order\Pricing\OrderPricingProviderInterface;
use Vespolina\Order\Pricing\AbstractOrderPricingProvider;


/**
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class DefaultOrderPricingProvider implements OrderPricingProviderInterface
{
    /** @var TaxProvider */
    protected $taxProvider;

    public function __construct(TaxProvider $taxProvider = null)
    {
        $this->taxProvider = $taxProvider;
    }

    // method that updates the pricing for the given order
    public function determineOrderPrices(OrderInterface $order, PricingContextInterface $pricingContext = null)
    {
        // not implemented
        if ($pricingContext === null) {
            $pricingContext = new PricingContext();
        }

        foreach ($order->getItems() as $item) {
            $this->determineOrderItemPrices($item, $pricingContext);
        }

        // summing it up
        $itemsTotalNet = 0;

        // updating prices for each item
        foreach ($order->getItems() as $item) {
            // this is the total value since we want to capture any calculations that happen on a specific item
            $itemsTotalNet += $item->getPrice('netValue');
        }

        $order->setPrice('totalNet', $itemsTotalNet);
        $order->setPrice('totalValue', $itemsTotalNet);

        // if pricing context has taxation enabled we calculate the taxes with the percentage set
        // example taxRates : 0.10 for 10%, 0.25 for 25%
        if (isset($pricingContext['partner'])) {
            if ($partner = $pricingContext['partner']) {
                /** @var $partner \Vespolina\Entity\Partner\Partner */
                if (count($partner->getPreferredPaymentProfile())) {
                    /** @var $address \Vespolina\Entity\Partner\AddressInterface */
                    $paymentProfile = $partner->getPreferredPaymentProfile();
                    $rate = $this->taxProvider->getTaxByState($paymentProfile->getBillingState());
                    $totalTax = $itemsTotalNet * $rate;
                    $order->setPrice('taxRate', $rate);
                    $order->setPrice('taxes', $totalTax);
                    $order->setPrice('totalValue', $itemsTotalNet + $totalTax);
                }
            }
        }
    }

    function addOrderHandler(OrderHandlerInterface $handler)
    {
    }

    function determineOrderItemPrices(ItemInterface $item, PricingContextInterface $pricingContext)
    {
        throw new \Exception('not implemented');
    }
}
