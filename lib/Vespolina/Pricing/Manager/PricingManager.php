<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\Pricing\Manager;

use Dough\Money\MultiCurrencyMoney;
use Symfony\Component\DependencyInjection\Container;
use Vespolina\Entity\Pricing\Element\TotalDoughValueElement;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class PricingManager implements PricingManagerInterface {

    protected $configurations;
    protected $defaultCurrency;

    public function __construct($defaultCurrency = 'USD') {

        $this->configurations = array();
        $this->defaultCurrency = $defaultCurrency;

        $this->addConfiguration('default', 'Vespolina\Entity\Pricing\PricingSet');
    }

    public function createPricingSet($name = 'default', array $pricingValues = array())
    {
        $pricingElements = array();

        if (!array_key_exists($name, $this->configurations)) {
            throw new \Exception('Could not load pricing set configuration ' . $name);
        }

        $configuration = $this->configurations[$name];

        //Create pricing elements from the configuration
        foreach ($configuration['pricingElementClasses'] as $pricingElementClass) {
            $pricingElements[] = new $pricingElementClass();
        }


        //Create the pricing set
        $pricingSet = new $configuration['pricingSetClass'](new TotalDoughValueElement(), array(), $pricingElements);

        foreach ($pricingValues as $pricingValueName => $pricingValue) {
            $pricingSet->set($pricingValueName, new MultiCurrencyMoney($pricingValue, $this->defaultCurrency));
        }


        return $pricingSet;
    }

    public function addConfiguration($name, $pricingSetClass, array $pricingElementClasses = array())
    {
        $this->configurations[$name] = array(
            'pricingSetClass' => $pricingSetClass,
            'pricingElementClasses' => $pricingElementClasses);
    }
}
