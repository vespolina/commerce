<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Gateway;

use Gateway\Query;
use Vespolina\Entity\Order\OrderInterface;

interface OrderGatewayInterface
{
    /**
     * Delete a Cart that has been persisted. The Order will be immediately flushed in the database
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     */
    function deleteOrder(OrderInterface $cart);

    /**
     * Find a Order by the value in a field or combination of fields
     *
     * @param \Gateway\Query $query
     *
     * @return \Vespolina\Entity\Order\OrderInterface|[]
     */
    function findOrders(Query $query);

    /**
     * Persist a Order that has been created.  The Order will be immediately flushed in the database
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     */
    function persistOrder(OrderInterface $cart);

    /**
     * Update a Order that has been persisted.  The Order will be immediately flushed in the database
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     */
    function updateOrder(OrderInterface $cart);
}
