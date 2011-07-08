<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\ProductNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class NodeTest extends WebTestCase
{
    public function testNode()
    {
        $nameProperty = new \ReflectionProperty(
          'Vespolina\ProductBundle\Model\ProductNode', 'name'
        );

        $nameProperty->setAccessible(true);

        $node1 = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\ProductNode');
        $node1->setName((integer)1);

        $this->assertInternalType('string', $nameProperty->getValue($node1), 'the name must be set as a string type');
        $this->assertTrue($node1->isRoot(), 'this is a root node');

        $this->assertFalse($node1, 'the child should be set in the parent');
        $this->assertFalse($node1, 'the parent should be set in the child');
    }
}
