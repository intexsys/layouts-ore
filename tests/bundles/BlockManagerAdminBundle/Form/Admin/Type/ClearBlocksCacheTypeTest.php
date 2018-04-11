<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\Form\Admin\Type;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearBlocksCacheType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ClearBlocksCacheTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Block\Block
     */
    private $blocks;

    public function setUp()
    {
        parent::setUp();

        $this->blocks = array(
            42 => new Block(
                array(
                    'id' => 42,
                    'availableLocales' => array('en'),
                    'locale' => 'en',
                )
            ),
            24 => new Block(
                array(
                    'id' => 24,
                    'availableLocales' => array('en'),
                    'locale' => 'en',
                )
            ),
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new ClearBlocksCacheType();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearBlocksCacheType::buildForm
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearBlocksCacheType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'blocks' => array(42),
        );

        $form = $this->factory->create(
            ClearBlocksCacheType::class,
            null,
            array('blocks' => $this->blocks)
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals(array('blocks' => array($this->blocks[42])), $form->getData());

        $view = $form->createView();

        $this->assertArrayHasKey('blocks', $view->vars);
        $this->assertEquals($this->blocks, $view->vars['blocks']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearBlocksCacheType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'blocks' => $this->blocks,
            )
        );

        $this->assertEquals($this->blocks, $options['blocks']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearBlocksCacheType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "blocks" with value 42 is expected to be of type "array", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidBlocks()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'blocks' => 42,
            )
        );
    }
}
