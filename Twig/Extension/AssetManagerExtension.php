<?php

namespace Vespolina\ProductBundle\Twig\Extension;
use Symfony\Comonent\HttpKernel\KernelInterface;
use Vespolina\ProductBundle\Model\AssetManager;

class AssetManagerExtension extends \Twig_Extension
{
    private $assetManager;

    public function __construct (AssetManager $assetManager)
    {
        $this->assetManager = $assetManager;
    }
    public function getFunctions()
    {
        return array(
                'assetManager' => new \Twig_Function_Method( $this,  'getAssetManager')
        );
    }

    public function getAssetManager()
    {
        return $this->assetManager;
    }

    public function getName()
    {
        return 'product_bundle';
    }
}
