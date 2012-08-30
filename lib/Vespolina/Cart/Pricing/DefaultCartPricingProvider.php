<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Cart\Pricing;

use Vespolina\Cart\Handler\CartHandlerInterface;
use Vespolina\Cart\Pricing\AbstractCartPricingProvider;
use Vespolina\Entity\Order\CartInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Pricing\PricingContext;
use Vespolina\Entity\Pricing\PricingContextInterface;
use Vespolina\Entity\ProductInterface;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class DefaultCartPricingProvider extends AbstractCartPricingProvider
{
    protected $fulfillmentPricingEnabled;

    public function __construct()
    {
        $this->fulfillmentPricingEnabled = true;
    }

    public function createPricingContext()
    {
        return new PricingContext();
    }

    public function determineCartPrices(CartInterface $cart, PricingContextInterface $pricingContext = null, $determineItemPrices = true)
    {
        if (null == $pricingContext) {
            $pricingContext = $this->createPricingContext();
            $pricingContext->set('totalNet', 0);
            $pricingContext->set('totalGross', 0);

        }
        //Check if the cart has taxation enabled
        $taxationEnabled = $cart->getAttribute('taxation_enabled');

        if ($taxationEnabled) {
            $pricingContext->set('totalTax', 0);
            $this->preparePricingContextForTaxation($pricingContext);
        }

        foreach ($cart->getItems() as $cartItem) {
            if ($determineItemPrices) {
                $this->determineCartItemPrices($cartItem, $pricingContext);
            }

            // Sum item level totals into the pricing context
            $this->sumItemPrices($cartItem, $pricingContext);
        }

        // Determine header level fulfillment costs (eg. one shot tax)
        if ($this->fulfillmentPricingEnabled) {
            $this->determineCartFulfillmentPrices($cart, $pricingContext);
        }

        $cartPricingSet = $cart->getPricing();
        $cartPricingSet->set('totalNet', $pricingContext->get('totalNet'));

        // Determine header level tax (eg. one shot tax)
        if ($taxationEnabled) {
            $this->determineCartTaxes($cart, $pricingContext);
            $totalGross =  $pricingContext->get('totalNet') +  $pricingContext->get('totalTax');
            $cartPricingSet->set('totalTax', $pricingContext->get('totalTax'));

        } else {
            $totalGross = $pricingContext->get('totalNet');
        }
        $cartPricingSet->set('totalGross', $totalGross);
        $cart->setTotalPrice($pricingContext->get('totalNet')); //Todo: remove
    }

    public function determineCartItemPrices(ItemInterface $cartItem, PricingContextInterface $pricingContext)
    {
        $handler = $this->getCartHandler($cartItem);
        $handler->determineCartItemPrices($cartItem, $pricingContext);
    }

    protected function determineCartFulfillmentPrices(CartInterface $cart, $pricingContext)
    {
        //Additional fulfillment to be applied not related to cart item taxes
        // eg. fixed fulfillment fee
    }

    protected function determineCartTaxes(CartInterface $cart, $pricingContext)
    {
        //Additional taxes to be applied not related to cart item taxes
    }

    protected function preparePricingContextForTaxation(PricingContextInterface $pricingContext)
    {
        //Find out if the tax zone was supplied
        $taxZone = $pricingContext->get('taxZone');

        if (null == $taxZone) {
            //Check if a fulfillment address was explicitly set
            $address = $pricingContext->get('fulfillmentAddress');

            if (null == $address) {
                //We we don't have a fulfillment address, we use now the customer's info
                $customer = $pricingContext->get('customer');

                if (null != $customer) {
                    $address = $customer->getAddresses()->first();  //Todo: should use delivery address
                }
            }

            if (null != $address) {
                //So we have an address, lookup the tax zone
                $taxZone =  $this->taxationManager->findTaxZoneByAddress($address);
                $pricingContext->set('taxZone', $taxZone);
            }
        }
    }

    protected function sumItemPrices(ItemInterface $cartItem, $pricingContext)
    {
        $cartItemPricingSet = $cartItem->getPricing();

        $totalNet = $pricingContext->get('totalNet') + $cartItemPricingSet->get('totalNet');
        $pricingContext->set('totalNet', $totalNet);
        $totalGross = $pricingContext->get('totalGross') + $cartItemPricingSet->get('totalGross');
        $pricingContext->set('totalGross', $totalGross);

        if (null != $this->taxationManager) {
            $totalTax = $pricingContext->get('totalTax') + $cartItemPricingSet->get('totalTax');
            $pricingContext->set('totalTax', $totalTax);
        }
    }
}
