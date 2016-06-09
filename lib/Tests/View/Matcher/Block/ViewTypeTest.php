<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Block;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\View\Matcher\Block\ViewType;
use Netgen\BlockManager\Tests\View\Stubs\View;

class ViewTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new ViewType();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Block\ViewType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $block = new Block(
            array(
                'viewType' => 'default',
            )
        );

        $view = new BlockView($block);

        self::assertEquals($expected, $this->matcher->match($view, $config));
    }

    /**
     * Provider for {@link self::testMatch}.
     *
     * @return array
     */
    public function matchProvider()
    {
        return array(
            array(array(), false),
            array(array('small'), false),
            array(array('default'), true),
            array(array('small', 'large'), false),
            array(array('small', 'default'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\ViewType::match
     */
    public function testMatchWithNoBlockView()
    {
        self::assertFalse($this->matcher->match(new View(new Value()), array()));
    }
}
