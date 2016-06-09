<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Page\Layout;

class BlockMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new BlockMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper::mapBlocks
     */
    public function testMapBlocks()
    {
        $data = array(
            array(
                'id' => 42,
                'layout_id' => 24,
                'zone_identifier' => 'bottom',
                'position' => 4,
                'definition_identifier' => 'paragraph',
                'parameters' => '{"param1": "param2"}',
                'view_type' => 'default',
                'item_view_type' => 'standard',
                'name' => 'My block',
                'status' => Layout::STATUS_PUBLISHED,
            ),
            array(
                'id' => 84,
                'layout_id' => 48,
                'zone_identifier' => 'top',
                'position' => 3,
                'definition_identifier' => 'title',
                'parameters' => '{"param1": 42}',
                'view_type' => 'small',
                'item_view_type' => 'standard',
                'name' => 'My other block',
                'status' => Layout::STATUS_PUBLISHED,
            ),
        );

        $expectedData = array(
            new Block(
                array(
                    'id' => 42,
                    'layoutId' => 24,
                    'zoneIdentifier' => 'bottom',
                    'position' => 4,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'param1' => 'param2',
                    ),
                    'viewType' => 'default',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Layout::STATUS_PUBLISHED,
                )
            ),
            new Block(
                array(
                    'id' => 84,
                    'layoutId' => 48,
                    'zoneIdentifier' => 'top',
                    'position' => 3,
                    'definitionIdentifier' => 'title',
                    'parameters' => array(
                        'param1' => 42,
                    ),
                    'viewType' => 'small',
                    'itemViewType' => 'standard',
                    'name' => 'My other block',
                    'status' => Layout::STATUS_PUBLISHED,
                )
            ),
        );

        self::assertEquals($expectedData, $this->mapper->mapBlocks($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper::mapCollectionReferences
     */
    public function testMapCollectionReferences()
    {
        $data = array(
            array(
                'block_id' => 1,
                'block_status' => Layout::STATUS_PUBLISHED,
                'collection_id' => 42,
                'collection_status' => Collection::STATUS_PUBLISHED,
                'identifier' => 'default',
                'start' => 5,
                'length' => 10,
            ),
            array(
                'block_id' => 2,
                'block_status' => Layout::STATUS_PUBLISHED,
                'collection_id' => 43,
                'collection_status' => Collection::STATUS_PUBLISHED,
                'identifier' => 'featured',
                'start' => 10,
                'length' => 15,
            ),
        );

        $expectedData = array(
            new CollectionReference(
                array(
                    'blockId' => 1,
                    'blockStatus' => Layout::STATUS_PUBLISHED,
                    'collectionId' => 42,
                    'collectionStatus' => Collection::STATUS_PUBLISHED,
                    'identifier' => 'default',
                    'offset' => 5,
                    'limit' => 10,
                )
            ),
            new CollectionReference(
                array(
                    'blockId' => 2,
                    'blockStatus' => Layout::STATUS_PUBLISHED,
                    'collectionId' => 43,
                    'collectionStatus' => Collection::STATUS_PUBLISHED,
                    'identifier' => 'featured',
                    'offset' => 10,
                    'limit' => 15,
                )
            ),
        );

        self::assertEquals($expectedData, $this->mapper->mapCollectionReferences($data));
    }
}
