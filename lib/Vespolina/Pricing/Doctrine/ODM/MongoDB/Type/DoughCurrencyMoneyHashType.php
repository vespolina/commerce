<?php
namespace Vespolina\Pricing\Doctrine\ODM\MongoDB\Type;

use Doctrine\ODM\MongoDB\Mapping\Types\Type;
use Dough\Money\MultiCurrencyMoney;

class DoughCurrencyMoneyHashType extends Type
{
    public function convertToDatabaseValue($value)
    {
        foreach ($value as $key => $item) {
            if ($item) {
                $value[$key] = Type::getType('dough_currency_money')->convertToDatabaseValue($item);
            }
        }

        return $value;
    }

    public function convertToPHPValue($value)
    {
        foreach ($value as $key => $item) {
            if ($item) {
                $value[$key] = Type::getType('dough_currency_money')->convertToPHPValue($item);
            }
        }

        return $value;
    }

    public function closureToMongo()
    {
        return '$process = $value;foreach ($process as $key => $value) { if ($value) { ' .
            Type::getType('dough_currency_money')->closureToMongo() .
            '$process[$key] = $return; } } $return = $process;';
    }

    public function closureToPHP()
    {
        return '$process = $value;foreach ($process as $key => $value) { if ($value) { ' .
            Type::getType('dough_currency_money')->closureToPHP() .
            '$process[$key] = $return; } } $return = $process;';
        return '$money = explode(\':\', $value);$return = new MultiCurrencyMoney($money[1], $money[0]);';
    }
}
