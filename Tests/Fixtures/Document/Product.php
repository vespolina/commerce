<?php
/**
 * (c) 2011 - 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Tests\Fixtures\Document;

use Vespolina\ProductBundle\Document\BaseProduct;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @author Richard Shank <develop@zestic.com>
 */
/**
 * @ODM\Document(collection="vespolina_product")
 */
class Product extends BaseProduct
{
    /** @ODM\Id */
    protected $id;

    /** @ODM\String */
    protected $name;

    public function __construct($identifierSetClass)
    {
        parent::__construct($identifierSetClass);
    }

    public function getId()
    {
        return $this->id;
    }
}
