<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Identifier;

use Vespolina\ProductBundle\Model\Node\IdentifierInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class BaseIdentifier implements IdentifierInterface
{
    protected $code;

    /**
     * @inheritdoc
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return $this->code;
    }
}
