<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference;
use Netgen\BlockManager\Persistence\Values\Page\Layout;

class BlockMapperTest extends \PHPUnit_Framework_TestCase
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
                'status' => Layout::STATUS_PUBLISHED,
                'collection_id' => 42,
                'identifier' => 'default',
                'start' => 5,
                'length' => 10,
            ),
            array(
                'block_id' => 2,
                'status' => Layout::STATUS_PUBLISHED,
                'collection_id' => 43,
                'identifier' => 'featured',
                'start' => 10,
                'length' => 15,
            ),
        );

        $expectedData = array(
            new CollectionReference(
                array(
                    'blockId' => 1,
                    'status' => Layout::STATUS_PUBLISHED,
                    'collectionId' => 42,
                    'identifier' => 'default',
                    'offset' => 5,
                    'limit' => 10,
                )
            ),
            new CollectionReference(
                array(
                    'blockId' => 2,
                    'status' => Layout::STATUS_PUBLISHED,
                    'collectionId' => 43,
                    'identifier' => 'featured',
                    'offset' => 10,
                    'limit' => 15,
                )
            ),
        );

        self::assertEquals($expectedData, $this->mapper->mapCollectionReferences($data));
    }
}
