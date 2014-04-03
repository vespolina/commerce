<?php

namespace Vespolina\Api\Interactor;

class Normalize
{
    public function process($data)
    {
        foreach ($data as $key => $value) {
            if ($key === 'prices') {
                $data[$key] = $this->handleTypeValues($value);
                continue;
            }
            if (is_array($value)) {
                $data[$key] = $this->process($value);
            }
        }

        return $data;
    }

    protected function handleTypeValues($data)
    {
        $response = [];
        foreach ($data as $set) {
            $response[$set['type']] = $set['value'];
        }

        return $response;
    }
} 