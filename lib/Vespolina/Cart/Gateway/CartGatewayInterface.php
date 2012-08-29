<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Cart\Gateway;

use Gateway\Query;
use Vespolina\Entity\Order\CartInterface;
use Vespolina\Entity\ItemInterface;

interface CartGatewayInterface
{
    /**
     * Delete a Cart that has been persisted. The Cart will be immediately flushed in the database
     *
     * @param Vespolina\Entity\Order\CartInterface $cart
     */
    function deleteCart(CartInterface $cart);

    /**
     * Find a Cart by the value in a field or combination of fields
     *
     * @param Gateway\Query $query
     *
     * @return single instance of or array of Vespolina\Entity\Order\CartInterface
     */
    function findCarts(Query $query);

    /**
     * Persist a Cart that has been created.  The Cart will be immediately flushed in the database
     *
     * @param Vespolina\Entity\Order\CartInterface $cart
     */
    function persistCart(CartInterface $cart);

    /**
     * Update a Cart that has been persisted.  The Cart will be immediately flushed in the database
     *
     * @param Vespolina\Entity\Order\CartInterface $cart
     */
    function updateCart(CartInterface $cart);
}
