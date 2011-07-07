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
        $node = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\ProductNode');

        $this->assert(, $node, 'this is a root node');
        $this->assert(, $node, 'the child should be set in the parent');
        $this->assert(, $node, 'the parent should be set in the child');
        $this->assert(, $node, 'a node must have a name');
    }
}
