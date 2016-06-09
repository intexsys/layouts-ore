<?php

namespace Netgen\BlockManager\Tests\Collection\QueryType\Configuration;

use Netgen\BlockManager\Collection\QueryType\Configuration\Form;

class FormTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType\Configuration\Form
     */
    protected $form;

    public function setUp()
    {
        $this->form = new Form('full', 'form_type', array('param1', 'param2'));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Form::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Form::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('full', $this->form->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Form::getType
     */
    public function testGetType()
    {
        self::assertEquals('form_type', $this->form->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\Configuration\Form::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(array('param1', 'param2'), $this->form->getParameters());
    }
}
