<?php

namespace Vespolina\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Vespolina\StoreBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function detailAction($slug)
    {
        $productManager = $this->container->get('vespolina.product_manager');

        $product = $productManager->findProductBySlug($slug);

        return $this->render('VespolinaProductBundle:Default:detail.html.twig', array('product' => $product));
    }

}
