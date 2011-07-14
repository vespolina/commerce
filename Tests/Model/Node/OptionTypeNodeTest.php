<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model\Node;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Node\OptionNode;
use Vespolina\ProductBundle\Model\Node\OptionTypeNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class OptionTypeNodeTest extends WebTestCase
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

        $otn = new OptionTypeNode();
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
}
