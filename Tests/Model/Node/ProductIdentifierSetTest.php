<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model\Node;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Node\ProductIdentifierSet;
use Vespolina\ProductBundle\Model\Node\OptionsSet;
use Vespolina\ProductBundle\Model\Node\OptionNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductIdentifierSetTest extends WebTestCase
{
    public function testAddOption()
    {
        $idSet = $this->createProductIdentifierSet();

        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Node\OptionsSetInterface',
            $idSet->getOptions(),
            'an OptionsSet object should be set'
        );

        $option = $this->createOptionNode('color', 'blue');
        $idSet->addOption($option);

        $this->assertEquals(
            1,
            $idSet->getOptions()->count(),
            'there should be one option in the set'
        );
    }

    protected function createProductIdentifierSet()
    {
        $idSet = $this->getMock('Vespolina\ProductBundle\Model\Node\ProductIdentifierSet', null, array(),'',false);

        $optionSet = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Node\OptionSet');
        
        $optionsProperty = new \ReflectionProperty(
          'Vespolina\ProductBundle\Model\Node\ProductIdentifierSet', 'options'
        );
        $optionsProperty->setAccessible(true);

        $optionsProperty->setValue($idSet, $optionSet);

        return $idSet;
    }

    protected function createOptionNode($type, $value)
    {
        $option = $this->getMock(
            'Vespolina\ProductBundle\Model\Node\OptionNode',
            array('getType', 'getValue'),
            array('Vespolina\ProductBundle\Model\Node\OptionsSet')
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
