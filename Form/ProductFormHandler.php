<?php
/**
* (c) 2011 Vespolina Project http://www.vespolina-project.org
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/
namespace Vespolina\ProductBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Vespolina\ProductBundle\Form\Model\CheckProduct;
use Vespolina\ProductBundle\Model\ProductManagerInterface;
/**
 * @author Richard Shank <develop@zestic.com>
 */
class ProductFormHandler
{
    protected $request;
    protected $productManager;
    protected $form;

    public function __construct(Form $form, Request $request, ProductManagerInterface $productManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->productManager = $productManager;
    }

    public function process()
    {
        $this->form->setData(new CheckProduct);

        if ('POST' == $this->request->getMethod()) {
            $data = $this->request->request->get($this->form->getName());
            $this->form->bind($data);

            if ($this->form->isValid()) {
                $product = $this->productManager->createProduct();
                $product->setName($data['name']);
                $product->setDescription($data['name']);

                $primaryIdentifier = $this->productManager->getPrimaryIdentifier();
                $identifier = new $primaryIdentifier;
                $identifier->setCode($data['identifier']);
                $identifierSet = $this->productManager->createIdentifierSet($identifier);
                
                $this->productManager->addIdentifierSetToProduct($identifierSet, $product);
                $this->productManager->updateProduct($product);
                return true;
            }
        }

        return false;
    }
}
