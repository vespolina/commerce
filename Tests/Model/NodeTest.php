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

        $node2 = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\ProductNode');
        $node2->setName('node 2');

        $node1->addChild($node2);
        $this->assertSame($node2, $node1->getChild('node 2'), 'the child should be set in the parent');
        $this->assertSame($node1, $node2->getParent(), 'the parent should be set in the child');
        $this->assertFalse($node2->isRoot(), 'node2 should not be a root node');

        $node3 = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\ProductNode');
        $node3->setName('node 3');

        $node1->addChild($node3);
        $this->assertEquals(2, count($node1->getChildren()), 'node1 should have 2 children');

        $node2->addChild($node3);
        $this->assertSame($node2, $node3->getParent(), 'node 3 should have node 2 as a parent now');
        $this->assertNull($node1->getChild('node 3'), 'node 1 should not have node 3 as a child now');
        $this->assertSame($node3, $node2->getChild('node 3'), 'node 2 should have node 3 as a child now');

        $node1->removeChild('node 2');
        $this->assertNull($node2->getParent(), 'removing a child should remove the parent in the child');

        $node1->addChild($node2);
        $node1->addChild($node3);
        $node1->clearChildren();
        $this->assertNull($node1->getChildren(), 'clear children should remove all children from node');
        $this->assertNull($node2->getParent(), 'clearing children should leave children without parent');
        $this->assertNull($node3->getParent(), 'clearing children should leave children without parent');

        $this->setExpectedException('InvalidArgumentException', 'The child node must have a name set');
        $node4 = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\ProductNode');
        $node1->addChild($node4);
    }
}
