<?php

namespace App\DataObjects\Product;

use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\Product;

class ProductAbsoluteWeightCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($object instanceof Product && $context->getFieldname() === 'absolute_weight' && $object->getWeight()) {
            $multiplier = [
                'ci_gram' => 1,
                'ci_kilogram' => 1000,
            ][$object->getWeight()->getUnit()->getId()];
            return $object->getWeight()->getValue() * $multiplier;
        }
        return "";
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        if ($object instanceof Product && $context->getFieldname() === 'absolute_weight' && $object->getWeight()) {
            $multiplier = [
                'ci_gram' => 1,
                'ci_kilogram' => 1000,
            ][$object->getWeight()->getUnit()->getId()];
            return $object->getWeight()->getValue() * $multiplier;
        }
        return "";
    }
}
