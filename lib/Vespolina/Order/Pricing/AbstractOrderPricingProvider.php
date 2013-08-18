<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Pricing;

use Vespolina\Order\Handler\OrderHandlerInterface;
use Vespolina\Order\Pricing\OrderPricingProviderInterface;
use Vespolina\Entity\Pricing\PricingSet;
use Vespolina\Entity\Order\ItemInterface;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
abstract class AbstractOrderPricingProvider implements OrderPricingProviderInterface
{
    protected $handlers;
    protected $taxationManager;

    public function __construct()
    {
        $this->handlers = array();
    }

    public function createPricingSet()
    {
        return new PricingSet();
    }

    public function addOrderHandler(OrderHandlerInterface $handler)
    {
        $types = (array)$handler->getTypes();
        foreach ($types as $type) {
            $this->handlers[$type] = $handler;
        }

        $handler->setTaxationManager($this->taxationManager);
    }

    public function setTaxationManager($taxationManager)
    {
        $this->taxationManager = $taxationManager;
    }

    protected function getOrderHandler(ItemInterface $cartItem)
    {
        $type = $cartItem->getProduct()->getType();
        if (!isset($this->handlers[$type])) {

            //Fall back to the default handler
            return $this->handlers['default'];
        }

        return $this->handlers[$type];
    }
}
