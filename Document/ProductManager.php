<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Document;

use Vespolina\ProductBundle\Document\Product;
use Vespolina\ProductBundle\Model\ProductManager as BaseProductManager;

/**
 * @author Richard Shank <develop@zestic.com>
 */
class ProductManager extends BaseProductManager
{
    protected $dm;
    protected $productRepo;
    
    public function __construct($dm)
    {
        $this->dm = $dm;
        $this->productRepo = $this->dm->getRepository('Vespolina\ProductBundle\Document\Product');
    }

    /**
     * @inheritdoc
     */
    public function createProduct()
    {
        // TODO: this will be using factories to allow for a number of different types of product classes
        return new Product();
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->productRepo->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function findProductById($id)
    {
        return $this->productRepo->find($id);
    }

    /**
     * @inheritdoc
     */
    public function findProductByIdentifier($name, $code)
    {

    }

    /**
     * @inheritdoc
     */
    public function updateProduct(ProductInterface $product, $andFlush = true)
    {
        $this->dm->persist($product);
        if ($andFlush) {
            $this->dm->flush();
        }
    }
}
