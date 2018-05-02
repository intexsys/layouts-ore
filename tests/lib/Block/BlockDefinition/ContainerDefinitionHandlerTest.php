<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class ContainerDefinitionHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandler
     */
    private $handler;

    public function setUp()
    {
        $this->handler = $this->getMockForAbstractClass(ContainerDefinitionHandler::class);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandler::getPlaceholderIdentifiers
     */
    public function testGetPlaceholderIdentifiers()
    {
        $this->assertNull($this->handler->getPlaceholderIdentifiers());
    }
}
