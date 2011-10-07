<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model\Node;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Option\OptionNode;
use Vespolina\ProductBundle\Model\Option\OptionGroup;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class OptionGroupTest extends WebTestCase
{
    public function testProductOptions()
    {
        $colorRed = new OptionNode();
        $colorRed->setName('colorRed');
        $colorRed->setType('color');
        $colorRed->setValue('red');

        $sizeXl = new OptionNode();
        $sizeXl->setName('sizeXl');
        $sizeXl->setType('size');
        $sizeXl->setValue('extra large');

        $otn = $this->createOptionGroup();
        // DO NOT SET THE NAME!
        $otn->addOption($colorRed);

        $this->assertEquals(
            'color',
            $otn->getName(),
            'if the name is not set, the name should be set to the type of the OptionNode'
        );

        $this->assertSame(
            $colorRed,
            $otn->getOption('red'),
            'the name of the option node should be set to the value'
        );

        $this->setExpectedException('UnexpectedValueException', 'All OptionsNodes in this type must be color');
        $otn->addOption($sizeXl);
    }

    protected function createOptionGroup()
    {
        $og = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Option\OptionGroup');
        return $og;
    }
}
