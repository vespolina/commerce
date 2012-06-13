<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

/**
 * @author Myke Hines <myke@webhines.com>
 */
abstract class Asset implements AssetInterface
{
    protected $label;
    protected $priority;
    protected $file_name;
    protected $height;
    protected $width;
    protected $mime;
    protected $type;
    protected $product;

    /**
     * Set the asset label
     *
     * @param $label
     */
    function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get the asset label.
     * @return label
     */
    function getLabel()
    {
        return $this->label;
    }


    /**
     * Set the asset priority
     *
     * @param $priority
     */
    function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * Get the asset priority.
     * @return priority
     */
    function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set the asset file_name
     *
     * @param $file_name
     */
    function setFileName($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * Get the asset file_name.
     * @return file_name
     */
    function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set the asset height
     *
     * @param $height
     */
    function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Get the asset height.
     * @return height
     */
    function getHeight()
    {
        return $this->height;
    }

    /**
     * Set the asset width
     *
     * @param $width
     */
    function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Get the asset width.
     * @return width
     */
    function getWidth()
    {
        return $this->width;
    }

    /**
     * Set the asset mime
     *
     * @param $mime
     */
    function setMime($mime)
    {
        $this->mime = $mime;
    }

    /**
     * Get the asset mime.
     * @return mime
     */
    function getMime()
    {
        return $this->mime;
    }

    /**
     * Set the asset product
     *
     * @param $product
     */
    function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Get the asset product.
     * @return product
     */
    function getProduct()
    {
        return $this->product;
    }

    /**
     * Set the asset type
     *
     * @param $type
     */
    function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get the asset type.
     * @return type
     */
    function getType()
    {
        return $this->type;
    }
}
