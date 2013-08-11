<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Handler;

use Vespolina\Entity\Channel\ChannelInterface;
use Vespolina\Entity\Product\MerchandiseInterface;
use Vespolina\Entity\Product\ProductInterface;

/**
 * Defines the interface for a merchandise handler defining the relationship between a Merchandise and its reference product
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
interface MerchandiseHandlerInterface
{

    /**
     * Create a merchandise item linking a product to a given sales channel
     *
     * @param ProductInterface $product
     * @param ChannelInterface $channel
     * @return Vespolina\Entity\Product\MerchandiseInterface
     */
    function createMerchandise(ProductInterface $product, ChannelInterface $channel);

    /**
     * (Re)Define the link between the merchandise and it's referenced product
     * Here you typically define which fields might need to overwritten for a specific channel
     *
     *
     * @param MerchandiseInterface $merchandise
     * @param ProductInterface $product
     */
    function link(MerchandiseInterface $merchandise, ProductInterface $product);

    /**
     * Return the merchandise type
     *
     * @return string
     */
    function getType();
}
