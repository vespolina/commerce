<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model\Node;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Node\FeatureNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class FeatureNodeTest extends WebTestCase
{
    public function testFeatureNode()
    {
        $titleNode = new FeatureNode();
        $titleNode->setType('title');
        $titleNode->setName('EIGHT53');

        $this->assertEquals(
            'eight53',
            $titleNode->getSearchTerm(),
            'the search term should be a lowercase version of the name'
        );

        $titleNode->setSearchTerm('different search term');
        $this->assertEquals(
            'different search term',
            $titleNode->setSearchTerm,
            'setting search term overrides previous set term'
        );

        $titleNode->setName('eigh53');
        $this->assertEquals(
            'different search term',
            $titleNode->getSearchTerm(),
            'if a term is already set, it should not be overwritten by setting the name'
        );

        $titleNode->setSearchTerm(21);
        $this->assertInternalType('string', $titleNode->getSearchTerm(), 'make sure the search is type cast as a string');
    }
}
