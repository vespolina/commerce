<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model\Node;

use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet;
use Vespolina\ProductBundle\Model\Option\OptionsSet;
use Vespolina\ProductBundle\Model\Option\Option;
use Vespolina\ProductBundle\Tests\ProductTestCommon;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductIdentifierSetTest extends ProductTestCommon
{
    public function testIdentifiers()
    {
        $idSet = $this->createProductIdentifierSet();
        $rc = new \ReflectionClass($idSet);
        $identifiersProperty = $rc->getProperty('identifiers');
        $identifiersProperty->setAccessible(true);
        
        $id1 = $this->createIdentifier('id1', 'abc123');
        $idSet->addIdentifier($id1);
        $identifiers = $identifiersProperty->getValue($idSet);
        $this->assertSame($id1, $identifiers['id1'], 'the identifier should be stored using the name as the key');

    }

    public function testMagicIdentifiers()
    {
        $this->markTestSkipped('not sure how to test __get() and __set()');
        $idSet = $this->createProductIdentifierSet();
        $rc = new \ReflectionClass($idSet);
        $identifiersProperty = $rc->getProperty('identifiers');
        $identifiersProperty->setAccessible(true);

        $testId = $this->createIdentifier('test', 'abc123');
        $this->assertFalse(
            method_exists($idSet, 'setTestIdentifier'),
            'ProductIdentifierSet::setTestIdentifier should not exist, if it is needed, change the Identifier type in the $testId in this test'
        );
        $idSet->setTestIdentifier($testId);
        $identifiers = $identifiersProperty->getValue($idSet);
        $this->assertSame($testId, $identifiers['test'], 'the identifier should be stored using the name as the key');
    }
}
