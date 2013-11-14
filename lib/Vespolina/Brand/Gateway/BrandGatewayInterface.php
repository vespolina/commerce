<?php

/**
 * (c) 2013 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Brand\Gateway;

use Vespolina\Entity\Brand\BrandInterface;
use Vespolina\Specification\SpecificationInterface;

/**
 * Defines the interface for a brand gateway to persist and retrieve brands
 *
 * The interface can be used for local gateways (eg. local mongo or orm database) but it might as well be
 * a remote ERP system
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
interface BrandGatewayInterface
{
    /**
     * Delete a Brand that has been persisted and optionally flush that link.
     * Systems that allow for a delayed flush can use the $andFlush parameter, other
     * systems would disregard the flag. The success of the process is returned.
     *
     * @param \Vespolina\Entity\BrandInterface $brand
     *
     * @param boolean $andFlush
     */
    function deleteBrand(BrandInterface $brand, $andFlush = false);

    /**
     * Find a brand by it's ID and ID type.  If no type has been given the default id strategy will be chosen.
     *
     * @param $id
     * @param null $type
     * @return mixed
     */
    function matchBrandById($id, $type = null);

    /**
     * Match multiple brands against the supplied specification
     *
     * @param SpecificationInterface $specification
     * @return mixed
     */
    function findAll(SpecificationInterface $specification);

    /**
     * Find one brand matching the requested specification
     *
     * @param SpecificationInterface $specification
     * @return mixed
     */
    function findOne(SpecificationInterface $specification);

    /**
     * Flush any changes to the gateway
     */
    function flush();

    /**
     * Persist a Brand that has been created and optionally flush that link.
     * Systems that allow for a delayed flush can use the $andFlush parameter, other
     * systems would disregard the flag. The success of the process is returned.
     *
     * @param Vespolina\Entity\Brand\BrandInterface $brand
     * @param boolean $andFlush
     */
    function persistBrand(BrandInterface $brand, $andFlush = false);

    /**
     * Update a Brand that has been persisted and optionally flush that link.
     * Systems that allow for a delayed flush can use the $andFlush parameter, other
     * systems would disregard the flag. The success of the process is returned.
     *
     * @param Vespolina\Entity\BrandInterface $brand
     *
     * @param boolean $andFlush
     */
    function updateBrand(BrandInterface $brand, $andFlush = false);
}
