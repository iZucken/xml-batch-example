<?php

namespace App\DataDefinitions\Interpreters;

use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Interpreter\InterpreterInterface;

class QuantityUnitInterpreter implements InterpreterInterface
{
    public function interpret(InterpreterContextInterface $context): mixed
    {
        if (empty($context->getValue())) {
            return null;
        }
        [$value, $unit] = explode(' ', trim($context->getValue()));
        if (empty($unit) || $value === '') {
            return null;
        }
        return new QuantityValue($value, Unit::getByAbbreviation($unit));
    }
}
