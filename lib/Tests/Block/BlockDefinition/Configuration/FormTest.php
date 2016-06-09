<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;

class FormTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form
     */
    protected $form;

    public function setUp()
    {
        $this->form = new Form('content', 'form_type', array('param1', 'param2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('content', $this->form->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::getType
     */
    public function testGetType()
    {
        self::assertEquals('form_type', $this->form->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(array('param1', 'param2'), $this->form->getParameters());
    }
}
