<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use PHPUnit\Framework\TestCase;

class ParameterStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\ParameterStruct
     */
    protected $struct;

    public function setUp()
    {
        $this->struct = $this->getMockForAbstractClass(ParameterStruct::class);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::__construct
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::getParameterValues
     */
    public function testDefaultProperties()
    {
        $this->assertEquals(array(), $this->struct->getParameterValues());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::setParameterValue
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::getParameterValues
     */
    public function testGetParameterValues()
    {
        $this->struct->setParameterValue('some_param', 'some_value');
        $this->struct->setParameterValue('some_other_param', 'some_other_value');

        $this->assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::setParameterValue
     */
    public function testOverwriteParameterValues()
    {
        $this->struct->setParameterValue('some_param', 'some_value');
        $this->struct->setParameterValue('some_param', 'new_value');

        $this->assertEquals(array('some_param' => 'new_value'), $this->struct->getParameterValues());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::getParameterValue
     */
    public function testGetParameterValue()
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->assertEquals('some_value', $this->struct->getParameterValue('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::getParameterValue
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetParameterValueThrowsInvalidArgumentException()
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->struct->getParameterValue('some_other_param');
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::hasParameterValue
     */
    public function testHasParameterValue()
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->assertTrue($this->struct->hasParameterValue('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::hasParameterValue
     */
    public function testHasParameterValueWithNoValue()
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->assertFalse($this->struct->hasParameterValue('some_other_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::fillValues
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::buildValue
     */
    public function testFillValues()
    {
        $parameterCollection = $this->buildParameterCollection();

        $initialValues = array(
            'css_class' => 'initial_css',
            'inner' => 'inner_initial',
        );

        $this->struct->fillValues($parameterCollection, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'initial_css',
                'css_id' => 'id',
                'compound' => true,
                'inner' => 'inner_initial',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::fillValues
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::buildValue
     */
    public function testFillValuesWithParameterValueInstances()
    {
        $parameterCollection = $this->buildParameterCollection();

        $initialValues = array(
            'css_class' => new ParameterValue(
                array(
                    'value' => 'initial_css',
                )
            ),
            'inner' => new ParameterValue(
                array(
                    'value' => 'inner_initial',
                )
            ),
        );

        $this->struct->fillValues($parameterCollection, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'initial_css',
                'css_id' => 'id',
                'compound' => true,
                'inner' => 'inner_initial',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::fillValues
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::buildValue
     */
    public function testFillValuesWithoutDefaults()
    {
        $parameterCollection = $this->buildParameterCollection();

        $initialValues = array(
            'css_class' => 'initial_css',
            'inner' => 'inner_initial',
        );

        $this->struct->fillValues($parameterCollection, $initialValues, false);

        $this->assertEquals(
            array(
                'css_class' => 'initial_css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner_initial',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @return \Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection
     */
    protected function buildParameterCollection()
    {
        $compoundParameter = new CompoundParameter(
            'compound',
            new ParameterType\Compound\BooleanType(),
            array(),
            false,
            true
        );

        $compoundParameter->setParameters(
            array(
                'inner' => new Parameter('inner', new ParameterType\TextLineType(), array(), false, 'inner_default'),
            )
        );

        $parameters = array(
            'css_class' => new Parameter('css_class', new ParameterType\TextLineType(), array(), false, 'css'),
            'css_id' => new Parameter('css_id', new ParameterType\TextLineType(), array(), false, 'id'),
            'compound' => $compoundParameter,
        );

        return new ParameterCollection($parameters);
    }
}
