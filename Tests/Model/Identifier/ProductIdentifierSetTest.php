<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model\Node;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet;
use Vespolina\ProductBundle\Model\Option\OptionsSet;
use Vespolina\ProductBundle\Model\Option\Option;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductIdentifierSetTest extends WebTestCase
{
    public function testAddOption()
    {
        $idSet = $this->createProductIdentifierSet();

        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Option\OptionSetInterface',
            $idSet->getOptionSet(),
            'an OptionSet object should be set'
        );

        $option = $this->createOption('color', 'blue');
        $idSet->addOption($option);

        $this->assertEquals(
            1,
            $idSet->getOptions()->count(),
            'there should be one option in the set'
        );
    }

    protected function createProductIdentifierSet()
    {
        $idSet = $this->getMock('Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet', null, array(),'',false);

        $optionSet = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Option\OptionSet');
        
        $optionsProperty = new \ReflectionProperty(
          'Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet', 'options'
        );
        $optionsProperty->setAccessible(true);

        $optionsProperty->setValue($idSet, $optionSet);

        return $idSet;
    }

    protected function createOption($type, $value)
    {
        $option = $this->getMock(
            'Vespolina\ProductBundle\Model\Option\Option',
            array('getType', 'getValue'),
            array('Vespolina\ProductBundle\Model\Option\OptionsSet')
        );
        $option->expects($this->any())
             ->method('getType')
             ->will($this->returnValue($type));
        $option->expects($this->any())
             ->method('getValue')
             ->will($this->returnValue($value));

        return $option;
    }

}
