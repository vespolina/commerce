<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Handler;

use Vespolina\Entity\Channel\ChannelInterface;
use Vespolina\Product\Handler\MerchandiseHandlerInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Entity\Product\MerchandiseInterface;

abstract class MerchandiseHandler implements MerchandiseHandlerInterface
{
    protected $merchandiseClass;
    protected $type = 'default';

    public function __construct($merchandiseClass)
    {
        $interfaceFQCN = 'Vespolina\Entity\Product\MerchandiseInterface';
        if (!in_array($interfaceFQCN, class_implements($merchandiseClass))) {
            throw new \Exception('Please have your merchandise implement interface '.$interfaceFQCN);
        }

        $this->merchandiseClass = $merchandiseClass;
    }

    public function createMerchandise(ProductInterface $product, ChannelInterface $channel)
    {
        $merchandise = new $this->merchandiseClass($product);

        $this->link($merchandise, $product);

        return $merchandise;
    }

    public function link(MerchandiseInterface $merchandise, ProductInterface $product)
    {

    }


    public function getType()
    {
        return $this->type;
    }
}
