<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model\Node;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Option\OptionSet;
use Vespolina\ProductBundle\Model\Option\Option;

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

        $os = $this->createOptionSet();

        foreach ($options as $type => $data) {
            foreach ($data as $value => $display) {
                $option = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Option\Option');
                $option->setDisplay($display);
                $option->setType($type);
                $option->setValue($value);
                $os->addOption($option);
            }
        }

        $childrenProperty = new \ReflectionProperty(
          'Vespolina\ProductBundle\Model\Option\OptionSet', 'children'
        );
        $childrenProperty->setAccessible(true);

        $this->assertArrayHasKey(
            'color',
            $childrenProperty->getValue($os),
            'the associative name should be set to type of the option'
        );

        $this->assertArrayHasKey(
            'size',
            $childrenProperty->getValue($os),
            'the associative name should be set to type of the option'
        );

        $this->assertEquals(3, count($os->getType('color')), 'there should be 3 color options');
        $this->assertEquals(4, count($os->getType('size')), 'there should be 4 size options');
        $this->assertEquals(
            'colorRed',
            $os->getOption('color', 'red')->getValue(),
            'an option can be returned by type and value'
        );
        $this->assertNull($os->getOption('bull', 'shit'), "return null when the type doesn't exists");

        $this->assertEquals('sizeXl', $os->getOptionByName('sizeXl')->getValue(), 'an option can be returned by name');
    }

    protected function createOptionSet()
    {
        $os = $this->getMock('Vespolina\ProductBundle\Model\Option\OptionSet', array('createOptionGroup'), array(), '', false);
        $os->expects($this->at(0))
             ->method('createOptionGroup')
             ->will($this->returnValue($this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Option\OptionGroup')));
        $os->expects($this->at(1))
             ->method('createOptionGroup')
             ->will($this->returnValue($this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Option\OptionGroup')));
        return $os;
    }
}
