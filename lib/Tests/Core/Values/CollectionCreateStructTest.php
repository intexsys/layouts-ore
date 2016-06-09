<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;

class CollectionCreateStructTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultProperties()
    {
        $collectionCreateStruct = new CollectionCreateStruct();

        self::assertNull($collectionCreateStruct->name);
    }

    public function testSetProperties()
    {
        $collectionCreateStruct = new CollectionCreateStruct(
            array(
                'name' => 'My collection',
            )
        );

        self::assertEquals('My collection', $collectionCreateStruct->name);
    }
}
