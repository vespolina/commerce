<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\RecurInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface RecurringInterface
{
    /**
     * Set the Recur object for this recurring product
     *
     * @param Vespolina\ProductBundle\Model\RecurInterface $recur
     */
    public function setRecur(RecurInterface $recur);

    /**
     * Return the Recur object for this recurring product
     *
     * @return Vespolina\ProductBundle\Model\RecurInterface
     */
    public function getRecur();
}
