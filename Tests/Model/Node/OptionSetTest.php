<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model\Node;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Node\OptionsSet;
use Vespolina\ProductBundle\Model\Node\OptionNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class OptionsSetTest extends WebTestCase
{
    public function testOptionsSet()
    {
        $options = array(
            'size' => array(
                'sizeSm' => 'small',
                'sizeMed' => 'medium',
                'sizeLg' => 'large',
                'sizeXl' => 'extra-large',
            ),
            'color' => array(
                'colorRed' => 'red',
                'colorGreen' => 'green',
                'colorBlue' => 'blue',
            ),
        );

        $po = new OptionsSet();

        foreach ($options as $type => $data) {
            foreach ($data as $name => $value) {
                $node = new OptionNode();
                $node->setName($name);
                $node->setType($type);
                $node->setValue($value);
                $po->addOption($node);
            }
        }

        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Node\OptionTypeNodeInterface',
            $po->getType('color'),
            'options should be grouped in an optionTypeNode'
        );

        $childrenProperty = new \ReflectionProperty(
          'Vespolina\ProductBundle\Model\Node\OptionsSet', 'children'
        );
        $childrenProperty->setAccessible(true);

        $this->assertArrayHasKey(
            'color',
            $childrenProperty->getValue($po),
            'the associative name should be set to type of the option'
        );

        $this->assertArrayHasKey(
            'size',
            $childrenProperty->getValue($po),
            'the associative name should be set to type of the option'
        );

        $this->assertEquals(3, count($po->getType('color')->getOptions()), 'there should be 3 color options');
        $this->assertEquals(4, count($po->getType('size')->getOptions()), 'there should be 4 size options');
        $this->assertEquals(
            'colorRed',
            $po->getOption('color', 'red')->getName(),
            'an option can be returned by type and value'
        );
        $this->assertNull($po->getOption('bull', 'shit'), "return null when the type doesn't exists");

        $this->assertEquals('sizeXl', $po->getOptionByName('sizeXl')->getName(), 'an option can be returned by name');
    }
}
