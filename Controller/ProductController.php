<?php
/**
* (c) 2011 Vespolina Project http://www.vespolina-project.org
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/
namespace Vespolina\ProductBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Vespolina\ProductBundle\Model\ProductInterface;

/**
* ProductBundle
*
* @author Joris de Wit <joris.w.dewit@gmail.com>
*/

class ProductController extends ContainerAware
{
    /**
     * Show all products
     */
    public function listAction()
    {
        $products = $this->container->get('vespolina.product_manager')->findBy(array());

        return $this->container->get('templating')->renderResponse('VespolinaProductBundle:Product:list.html.'.$this->getEngine(), array('products' => $products));
    }

    /**
     * Show one product by object id
     */
    public function showAction($id)
    {
        $product = $this->container->get('vespolina.product_manager')->findProductById($id);
        return $this->container->get('templating')->renderResponse('VespolinaProductBundle:Product:show.html.'.$this->getEngine(), array('product' => $product));
    }

    /**
     * Edit one product, show the edit form
     */
    public function editAction($id)
    {
        $product = $this->container->get('vespolina.product_manager')->findProductById($id);
        $form = $this->container->get('vespolina.product.form');
        $form->setData($product);

        return $this->container->get('templating')->renderResponse('VespolinaProductBundle:Product:edit.html.'.$this->getEngine(), array(
            'form'      => $form,
            'id'       => $product->getId()
        ));
    }

    /**
     * Update a product
     */
    public function updateAction($id)
    {
        $product = $this->container->get('vespolina.product_manager')->findProductById($id);
        $form = $this->container->get('vespolina.product.form');
        $form->bind($this->container->get('request'), $product);

        if ($form->isValid()) {
            $this->container->get('vespolina.product_manager')->updateProduct($product);
            $this->setFlash('vespolina_product_update', 'success');
            $productUrl = $this->generateUrl('vespolina_product_show', array('sku' => $product->getSKU()));
            return new RedirectResponse($productUrl);
        }

        return $this->container->get('templating')->renderResponse('VespolinaProductBundle:Product:edit.html.'.$this->getEngine(), array(
            'form'      => $form,
            'sku'       => $product->getSKU()
        ));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        $product = $this->container->get('vespolina.product_manager')->createProduct();
        $form = $this->container->get('vespolina.product.form');
        $form->setData($product);

        return $this->container->get('templating')->renderResponse('VespolinaProductBundle:Product:new.html.'.$this->getEngine(), array(
            'form' => $form->createView()
        ));
    }

    /**
     * Create a product
     */
    public function createAction()
    {
        $form = $this->container->get('vespolina.product.form');
        $formHandler = $this->container->get('vespolina.product.form.handler');

        $process = $formHandler->process();
        if ($process) {
            $user = $form->getData();

            $this->setFlash('vespolina_product_created', 'success');
            $url = $this->container->get('router')->generate('vespolina_product_list');

            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('VespolinaProductBundle:Product:new.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
        ));
    }

    protected function getEngine()
    {
        return 'twig'; // HACK ALERT!
//        return $this->container->getParameter('vespolina.template.engine');
    }
}
