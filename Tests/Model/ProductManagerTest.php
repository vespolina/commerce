<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Product;
use Vespolina\ProductBundle\Model\Node\ProductIdentifiers;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductManagerTest extends WebTestCase
{
    protected $mgr;
    protected $product;

    protected function setUp()
    {
        $this->mgr = $this->createProductManager();
        $this->product = new Product();
    }

    public function testPrimaryIdentifier()
    {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

        $this->assertSame(
            '\Vespolina\ProductBundle\Model\Node\IdentifierNode',
            $this->mgr->getPrimaryIdentifier(),
            "the primary identifier class name must have a leading \\"
        );

        /* exceptions */

        $this->setExpectedException(
            'InvalidArgumentException',
            'The primary identifier must be an instance of Vespolina\ProductBundle\Node\IdentifierNodeInterface'
        );
        $product->setPrimaryIdentifier('Vespolina\ProductBundle\Model\Product');
    }

    public function testIdentifiersToProduct()
    {
        $identifiers = new ProductIdentifiers();

        $this->mgr->addIdentifiersToProduct($identifiers, $this->product);

        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $this->product->getIdentifiers(),
            'the identifiers should be stored in an ArrayCollection'
        );

        $this->assertSame($identifiers, $this->product->getIdentifiers()->first(), 'identifier set should be put in the product');
        $this->assertSame(
            $identifiers,
            $this->product->getIdentifiers()->get('sku1234'),
            'the index for the identifier set should be the code for the primary identifier, sku'
        );
        
        $identifiers2 = new ProductIdentifiers();

        $this->mgr->addIdentifiersToProduct($identifiers2, $this->product);
        $this->assertEquals(2, $this->product->getIdentifiers()->count(), 'a second identifier set should put in the product');

        $this->mgr->removeIdentifiersFromProduct($identifiers2, $this->product);
        $this->assertEquals(1, $this->product->getIdentifiers()->count(), 'remove identifiers should leave product with one less');

        $this->mgr->removeIdentifiersFromProduct('sku1234', $this->product);
        $this->assertEquals(0, $this->product->getIdentifiers()->count(), 'remove identifiers by primary identifier code should work also');

        /* exceptions */
        $mgr = $this->createProductManager();
        $pi = $this->createProductIdentifiers('abcdefg');
        $this->setExpectedException('UnexpectedValueException', 'The primary identifier type has not been set');
        $mgr->addIdentifiersToProduct($pi, $this->product);

        $this->setExpectedException(
            'UnexpectedValueException',
            'The primary identifier is not in this Vespolina\ProductBundle\Node\ProductIdentifiers instance'
        );
        $mgr->addIdentifiersToProduct($pi, $this->product);
    }

    public function testSearchForProductByIdentifier()
    {
        // search by identifier should return a product set up with the specific information for that identifier
        // full results flag returns the full data set for the product
    }

    protected function createProductManager()
    {
        $mgr = $this->getMockforAbstractClass('Vespolina\ProductBundle\Model\ProductManager');

        $primaryIdentifier = new \ReflectionProperty('Vespolina\ProductBundle\Model\ProductManager', 'primaryIdentifier');
        $primaryIdentifier->setAccessible(true);
        $primaryIdentifier->setValue($mgr, 'Vespolina\ProductBundle\Model\Node\IdentifierNode');
        return $mgr;
    }

    protected function createProductIdentifiers($code)
    {
        $pi = new ProductIdentifiers();
        $pi = $this->getMock('Vespolina\ProductBundle\Model\Node\IdentifierNode', array('getCode', 'getName'));
        $pi->expects($this->any())
             ->method('getCode')
             ->will($this->returnValue($code));
        $pi->expects($this->any())
             ->method('getName')
             ->will($this->returnValue($code));
        return $pi;
    }
}
