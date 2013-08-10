<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Pricing\Manager;

use Dough\Money\MultiCurrencyMoney;
use Symfony\Component\DependencyInjection\Container;
use Vespolina\Entity\Pricing\Element\TotalDoughValueElement;
use Vespolina\Entity\Pricing\PricingSetInterface;

/**
 * The pricing manager manages the creation of pricing sets which
 * are attached to entities such as products, carts and orders
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class PricingManager implements PricingManagerInterface {

    protected $configurations;
    protected $defaultCurrency;

    /**
     * Constructor
     *
     * @param string $defaultCurrency
     */
    public function __construct($defaultCurrency = 'USD') {

        $this->configurations = array();
        $this->defaultCurrency = $defaultCurrency;

        $this->addConfiguration('default', 'Vespolina\Entity\Pricing\PricingSet');
    }

    /**
     * Create a pricing set of type $type
     * Optionally pass pricing values to the newly created pricing set
     *
     * @param string $type
     * @param array $pricingValues
     * @return Vespolina\Entity\Pricing\PricingSetInterface
     * @throws \Exception
     */
    public function createPricingSet($type = 'default', array $pricingValues = array())
    {
        $pricingElements = array();

        if (!array_key_exists($type, $this->configurations)) {
            throw new \Exception('Could not load pricing set configuration ' . $type);
        }

        $configuration = $this->configurations[$type];

        //Create pricing elements from the configuration
        foreach ($configuration['pricingElementClasses'] as $pricingElementClass) {
            $pricingElements[] = new $pricingElementClass();
        }

        //Create the pricing set
        $pricingSet = new $configuration['pricingSetClass'](new TotalDoughValueElement(), array(), $pricingElements);

        //Add default values to the pricing set
        foreach ($pricingValues as $pricingValueName => $pricingValue) {
            $pricingSet->set($pricingValueName, new MultiCurrencyMoney($pricingValue, $this->defaultCurrency));
        }

        return $pricingSet;
    }

    /**
     * Add a new pricing configuration type
     *
     * @param $type
     * @param $pricingSetClass
     * @param array $pricingElementClasses
     * @return mixed|void
     */
    public function addConfiguration($type, $pricingSetClass, array $pricingElementClasses = array())
    {
        $this->configurations[$type] = array(
            'pricingSetClass' => $pricingSetClass,
            'pricingElementClasses' => $pricingElementClasses);
    }

    /**
     * Process an existing pricing set
     *
     * For instance a product pricing instance needs additional processing to inject the
     * current tax rate of the customer to display the correct gross price
     *
     * @param PricingSetInterface $pricingSet
     * @param null $context
     * @return mixed
     */
    public function process(PricingSetInterface $pricingSet, $context = null)
    {
        //Temporary hack to assure valueElement exists (when loaded from persistence)
        $rp = new \ReflectionProperty($pricingSet, 'valueElement');
        $rp->setAccessible(true);

        if (null == $rp->getValue($pricingSet)) {
            $rp->setValue($pricingSet, new TotalDoughValueElement());
        }
        $rp->setAccessible(false);

        //Process the pricing set and inject the context
        $newPricingSet = $pricingSet->process($context);

        return $newPricingSet;
    }
}
