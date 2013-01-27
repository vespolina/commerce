<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\Order\Gateway;

use Molino\SelectQueryInterface;
use Vespolina\Entity\Order\ItemInterface;

interface ItemGatewayInterface
{
    /**
     * @param $type
     * @param null $queryClass
     * @return mixed
     */
    function createQuery($type, $queryClass = null);

    /**
     * Delete a Cart that has been persisted. The Item will be immediately flushed in the database
     *
     * @param \Vespolina\Entity\Item\ItemInterface $item
     */
    function deleteItem(ItemInterface $item);

    /**
     * Find a Item by the value in a field or combination of fields
     *
     * @param \Molino\SelectQueryInterface $query
     *
     * @return \Vespolina\Entity\Item\ItemInterface|[]
     */
    function findItems(SelectQueryInterface $query);

    /**
     * Persist a Item that has been created.  The Item will be immediately flushed in the database
     *
     * @param \Vespolina\Entity\Item\ItemInterface $item
     */
    function persistItem(ItemInterface $item);

    /**
     * Update a Item that has been persisted.  The Item will be immediately flushed in the database
     *
     * @param \Vespolina\Entity\Item\ItemInterface $item
     */
    function updateItem(ItemInterface $item);
}
