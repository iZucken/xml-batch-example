<?php

namespace unit\Features\Products\VO;

use App\Features\Products\VO\SIWeight;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Features\Products\VO\SIWeight
 */
class SIWeightTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getValue
     * @covers ::getUnits
     */
    public function test__construct()
    {
        $weight = new SIWeight('1 g');
        $this->assertEquals(1.0, $weight->getValue());
        $this->assertEquals(SIWeight::GRAM, $weight->getUnits());
        $weight = new SIWeight('10 kg');
        $this->assertEquals(10.0, $weight->getValue());
        $this->assertEquals(SIWeight::KILOGRAM, $weight->getUnits());
        foreach (['1', '1 zg'] as $case) {
            $this->expectException(\InvalidArgumentException::class);
            new SIWeight($case);
        }
    }

    /**
     * @covers ::__toString
     */
    public function test__toString()
    {
        foreach (['1 g', '1 kg'] as $case) {
            $this->assertEquals($case, (string)new SIWeight($case));
        }
    }

    /**
     * @covers ::toGrams
     */
    public function testToGrams()
    {
        foreach (['1 g' => 1, '1 kg' => 1000] as $case => $value) {
            $this->assertEquals($value, (new SIWeight($case))->toGrams());
        }
    }
}
