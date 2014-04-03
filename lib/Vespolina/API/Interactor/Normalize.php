<?php

namespace Vespolina\API\Interactor;

/**
 * Class Serialize
 * @package Vespolina\API\Interactor
 */
class Serialize 
{
    protected $serializer;

    public function __construct($serializer)
    {
        $this->serializer = $serializer;
    }

    public function process($data)
    {
        if (is_object($data)) {
            $this->serializer->
        }
    }
} 