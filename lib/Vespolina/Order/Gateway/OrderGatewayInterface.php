<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Gateway;

use Molino\SelectQueryInterface;
use Vespolina\Entity\Order\OrderInterface;

interface OrderGatewayInterface
{
    /**
     * @param $type
     * @param null $queryClass
     * @return mixed
     */
    function createQuery($type, $queryClass = null);

    /**
     * Delete a Cart that has been persisted. The Order will be immediately flushed in the database
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     */
    function deleteOrder(OrderInterface $cart);

    /**
     * Find a Order by the value in a field or combination of fields
     *
     * @param \Molino\SelectQueryInterface $query
     *
     * @return \Vespolina\Entity\Order\OrderInterface|[]
     */
    function findOrders(SelectQueryInterface $query);

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
