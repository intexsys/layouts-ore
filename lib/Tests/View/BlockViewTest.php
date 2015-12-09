<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;

class BlockViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\BlockView::setBlock
     * @covers \Netgen\BlockManager\View\BlockView::getBlock
     */
    public function testSetBlock()
    {
        $block = new Block(array('id' => 42));

        $view = new BlockView();
        $view->setParameters(array('block' => 42));
        $view->setBlock($block);

        self::assertEquals($block, $view->getBlock());
        self::assertEquals(array('block' => $block), $view->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\View\BlockView::getAlias
     */
    public function testGetAlias()
    {
        $view = new BlockView();
        self::assertEquals('block_view', $view->getAlias());
    }
}
