<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model\Node;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Node\OptionSet;
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

        $os = $this->createOptionSet();

        foreach ($options as $type => $data) {
            foreach ($data as $name => $value) {
                $node = new OptionNode();
                $node->setName($name);
                $node->setType($type);
                $node->setValue($value);
                $os->addOption($node);
            }
        }

        $childrenProperty = new \ReflectionProperty(
          'Vespolina\ProductBundle\Model\Node\OptionSet', 'children'
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
            $os->getOption('color', 'red')->getName(),
            'an option can be returned by type and value'
        );
        $this->assertNull($os->getOption('bull', 'shit'), "return null when the type doesn't exists");

        $this->assertEquals('sizeXl', $os->getOptionByName('sizeXl')->getName(), 'an option can be returned by name');


        $this->assertEquals(7, $os->count(), 'count should return the total number of options stored');

        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $os->getOptions(),
            'the identifiers should be stored in an ArrayCollection'
        );
    }

    protected function createOptionSet()
    {
        $os = $this->getMock('Vespolina\ProductBundle\Model\Node\OptionSet');
        return $os;
    }
}
