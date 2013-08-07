<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Channel\ChannelInterface;
use Vespolina\Entity\Product\Merchandise;
use Vespolina\Specification\SpecificationInterface;

class ChannelSpecification implements SpecificationInterface
{
    protected $channel;

    public function __construct(ChannelInterface $channel)
    {
        $this->channel = $channel;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function isSatisfiedBy($product)
    {
        if ($product instanceof Merchandise) {

            /* @var $merchandise Vespolina\Entity\Product\Merchandise */
            $merchandise = $product;

            return $merchandise->getChannel() == $this->channel;
        }

        return false;   //A product (non merchandise) instance can never match the channel specified

    }
}