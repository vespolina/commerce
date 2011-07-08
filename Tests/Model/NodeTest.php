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
        $node1 = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\ProductNode');
        $node1->setName('node 1');

        $this->assertEquals('node 1', $node1->getName(), 'a node must have a name');
        $this->assertTrue($node1->isRoot(), 'this is a root node');

        $this->assertFalse($node1, 'the child should be set in the parent');
        $this->assertFalse($node1, 'the parent should be set in the child');
    }
}
