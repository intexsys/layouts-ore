<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use Netgen\BlockManager\Parameters\ParameterValue;
use PHPUnit\Framework\TestCase;

class ParameterValueTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getName
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getParameter
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getValue
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::isEmpty
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::__toString
     */
    public function testSetDefaultProperties()
    {
        $parameterValue = new ParameterValue();

        $this->assertNull($parameterValue->getName());
        $this->assertNull($parameterValue->getParameter());
        $this->assertNull($parameterValue->getValue());
        $this->assertNull($parameterValue->isEmpty());
        $this->assertEquals('', (string) $parameterValue);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getName
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getParameter
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::getValue
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::isEmpty
     * @covers \Netgen\BlockManager\Parameters\ParameterValue::__toString
     */
    public function testSetProperties()
    {
        $parameter = new Parameter(
            array(
                'type' => new TextType(),
            )
        );

        $parameterValue = new ParameterValue(
            array(
                'name' => 'param_name',
                'parameter' => $parameter,
                'value' => 42,
                'isEmpty' => false,
            )
        );

        $this->assertEquals('param_name', $parameterValue->getName());
        $this->assertEquals($parameter, $parameterValue->getParameter());
        $this->assertEquals(42, $parameterValue->getValue());
        $this->assertFalse($parameterValue->isEmpty());
        $this->assertEquals('42', (string) $parameterValue);
    }
}
