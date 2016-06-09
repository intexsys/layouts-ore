<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Twig_SimpleFunction;
use Twig_TokenParser;
use Twig_Template;
use Exception;

class NetgenBlockManagerExtensionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewRendererMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fragmentHandlerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->globalHelperMock = $this->createMock(GlobalHelper::class);

        $this->viewRendererMock = $this->createMock(RendererInterface::class);

        $this->fragmentHandlerMock = $this->createMock(FragmentHandler::class);

        $this->extension = new NetgenBlockManagerExtension(
            $this->globalHelperMock,
            $this->viewRendererMock,
            $this->fragmentHandlerMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::getName
     */
    public function testGetName()
    {
        self::assertEquals('netgen_block_manager', $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::getGlobals
     */
    public function testGetGlobals()
    {
        self::assertEquals(
            array(
                'ngbm' => $this->globalHelperMock,
            ),
            $this->extension->getGlobals()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::getFunctions
     */
    public function testGetFunctions()
    {
        self::assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            self::assertInstanceOf(Twig_SimpleFunction::class, $function);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::getTokenParsers
     */
    public function testGetTokenParsers()
    {
        self::assertNotEmpty($this->extension->getTokenParsers());

        foreach ($this->extension->getTokenParsers() as $tokenParser) {
            self::assertInstanceOf(Twig_TokenParser::class, $tokenParser);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::renderBlock
     */
    public function testRenderBlock()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_VIEW),
                $this->equalTo(array('param' => 'value'))
            )
            ->will($this->returnValue('rendered block'));

        self::assertEquals(
            'rendered block',
            $this->extension->renderBlock(
                new Block(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_VIEW
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::renderItem
     */
    public function testRenderItem()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(ViewInterface::CONTEXT_VIEW),
                $this->equalTo(array('viewType' => 'viewType', 'param' => 'value'))
            )
            ->will($this->returnValue('rendered item'));

        self::assertEquals(
            'rendered item',
            $this->extension->renderItem(
                new Item(),
                'viewType',
                array('param' => 'value'),
                ViewInterface::CONTEXT_VIEW
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::displayZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::setDebug
     * @expectedException \Exception
     */
    public function testDisplayZoneThrowsException()
    {
        $twigTemplateMock = $this->createMock(Twig_Template::class);

        $twigTemplateMock
            ->expects($this->at(0))
            ->method('displayBlock')
            ->will($this->throwException(new Exception()));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject');

        $this->extension->setDebug(true);
        $this->extension->displayZone(
            new Zone(
                array(
                    'blocks' => array(
                        new Block(array('definitionIdentifier' => 'block_definition')),
                        new Block(array('definitionIdentifier' => 'twig_block')),
                        new Block(array('definitionIdentifier' => 'block_definition')),
                    ),
                )
            ),
            ViewInterface::CONTEXT_VIEW,
            $twigTemplateMock,
            array(),
            array()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::displayZone
     */
    public function testDisplayZoneThrowsExceptionInDebugMode()
    {
        $twigTemplateMock = $this->createMock(Twig_Template::class);

        $twigTemplateMock
            ->expects($this->at(0))
            ->method('displayBlock')
            ->will($this->throwException(new Exception()));

        $this->viewRendererMock
            ->expects($this->at(0))
            ->method('renderValueObject')
            ->will($this->returnValue('rendered block 1'));

        $this->viewRendererMock
            ->expects($this->at(1))
            ->method('renderValueObject')
            ->will($this->returnValue('rendered block 2'));

        ob_start();

        $this->extension->displayZone(
            new Zone(
                array(
                    'blocks' => array(
                        new Block(array('definitionIdentifier' => 'block_definition')),
                        new Block(array('definitionIdentifier' => 'twig_block')),
                        new Block(array('definitionIdentifier' => 'block_definition')),
                    ),
                )
            ),
            ViewInterface::CONTEXT_VIEW,
            $twigTemplateMock,
            array(),
            array()
        );

        $renderedTemplate = ob_get_contents();

        ob_get_clean();

        self::assertEquals(
            'rendered block 1rendered block 2',
            $renderedTemplate
        );
    }
}
