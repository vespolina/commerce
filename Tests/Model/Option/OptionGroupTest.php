<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model\Node;

use Vespolina\ProductBundle\Model\Option\Option;
use Vespolina\ProductBundle\Model\Option\OptionGroup;
use Vespolina\ProductBundle\Tests\ProductTestCommon;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class OptionGroupTest extends ProductTestCommon
{
    public function testProductOptions()
    {
        $colorRed = $this->createOption('red', 'color', 'colorRed');

        $sizeXl = $this->createOption('extra large', 'size', 'sizeXl');

        $og = $this->createOptionGroup();
        // DO NOT SET THE NAME IN THE GROUP!
        $og->addOption($colorRed);

        $this->assertEquals(
            'color',
            $og->getName(),
            'if the name is not set, the name should be set to the type of the Option'
        );

        $this->assertSame(
            $colorRed,
            $og->getOption('colorRed'),
            'the option value should be found by its value'
        );

        $this->assertSame(
            $colorRed,
            $og->getOptionByDisplay('red'),
            'the name of the option node should be set to the value'
        );

        $noTypeGreen = $this->createOption('green', null, 'colorGreen');
        $og->addOption($noTypeGreen);

        $this->assertSame(
            $noTypeGreen,
            $og->getOption('colorGreen'),
            'if the type is not set, the option should be added to the group'
        );

        $this->assertSame(
            'color',
            $noTypeGreen->getType(),
            'if the type is not set, type option type should be set to the name of the group'
        );

        $this->setExpectedException('UnexpectedValueException', 'All OptionsNodes in this type must be color');
        $og->addOption($sizeXl);

        $noTypeBlue = $this->createOption('blue', null, 'colorBlue');
        $og = $this->createOptionGroup();
        // DO NOT SET THE NAME IN THE GROUP!
        $this->setExpectedException('UnexpectedValueException', 'The OptionGroup must have the name set or the Option must have the group type set');
        $og->addOption($noTypeBlue);
    }
}
