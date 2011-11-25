<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\Option\OptionGroupInterface;
/**
 * @author Richard Shank <develop@zestic.com>
 */
interface ProductAdminManagerInterface
{
    /**
     * Find a collection of option groups by the criteria
     *
     * @param array $criteria
     * @param mixed $orderBy
     * @param mixed $limit
     * @param mixed $offset
     *
     * @return array
     */
    function findOptionGroupsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Find an OptionGroup by its object identifier
     *
     * @param $id
     * @return Vespolina\ProductBundle\Model\Option\OptionGroupInterface
     */
    function findOptionGroupById($id);

    /**
     * Delete an OptionGroup with the passed  object identifier
     *
     * @param $id
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function deleteOptionGroupById($id, $andFlush = true);

    /**
     * Delete a persisted object
     *
     * @param $object peristed object
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function delete($object, $andFlush = true);

    /**
     * Update and persist
     *
     * @param $object persistable object
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function update($object, $andFlush = true);
}
