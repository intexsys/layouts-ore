<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class CollectionMapperTest extends ServiceTestCase
{
    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->collectionMapper = $this->createCollectionMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapCollection
     */
    public function testMapCollection()
    {
        $persistenceCollection = new Collection(
            array(
                'id' => 2,
                'status' => Value::STATUS_PUBLISHED,
                'type' => APICollection::TYPE_DYNAMIC,
                'shared' => true,
                'name' => null,
            )
        );

        $collection = $this->collectionMapper->mapCollection($persistenceCollection);

        $this->assertInstanceOf(APICollection::class, $collection);
        $this->assertEquals(2, $collection->getId());
        $this->assertEquals(APICollection::TYPE_DYNAMIC, $collection->getType());
        $this->assertTrue($collection->isShared());
        $this->assertNull(null, $collection->getName());
        $this->assertEquals(Value::STATUS_PUBLISHED, $collection->getStatus());

        foreach ($collection->getItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getManualItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        foreach ($collection->getOverrideItems() as $item) {
            $this->assertInstanceOf(APIItem::class, $item);
        }

        $this->assertEquals(
            count($collection->getItems()),
            count($collection->getManualItems()) + count($collection->getOverrideItems())
        );

        foreach ($collection->getQueries() as $query) {
            $this->assertInstanceOf(APIQuery::class, $query);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapItem
     */
    public function testMapItem()
    {
        $persistenceItem = new Item(
            array(
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'position' => 1,
                'type' => APIItem::TYPE_OVERRIDE,
                'valueId' => '12',
                'valueType' => 'ezcontent',
            )
        );

        $item = $this->collectionMapper->mapItem($persistenceItem);

        $this->assertInstanceOf(APIItem::class, $item);
        $this->assertEquals(1, $item->getId());
        $this->assertEquals(42, $item->getCollectionId());
        $this->assertEquals(1, $item->getPosition());
        $this->assertEquals(APIItem::TYPE_OVERRIDE, $item->getType());
        $this->assertEquals('12', $item->getValueId());
        $this->assertEquals('ezcontent', $item->getValueType());
        $this->assertEquals(Value::STATUS_PUBLISHED, $item->getStatus());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper::mapQuery
     */
    public function testMapQuery()
    {
        $persistenceQuery = new Query(
            array(
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'position' => 1,
                'identifier' => 'my_search',
                'type' => 'ezcontent_search',
                'parameters' => array('param' => 'value'),
            )
        );

        $query = $this->collectionMapper->mapQuery($persistenceQuery);

        $this->assertEquals(
            $this->queryTypeRegistry->getQueryType('ezcontent_search'),
            $query->getQueryType()
        );

        $this->assertInstanceOf(APIQuery::class, $query);
        $this->assertEquals(1, $query->getId());
        $this->assertEquals(42, $query->getCollectionId());
        $this->assertEquals(1, $query->getPosition());
        $this->assertEquals('my_search', $query->getIdentifier());
        $this->assertEquals(Value::STATUS_PUBLISHED, $query->getStatus());

        $this->assertEquals(
            array(
                'offset' => new ParameterValue(
                    array(
                        'identifier' => 'offset',
                        'parameter' => $query->getQueryType()->getParameters()['offset'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('integer'),
                        'value' => null,
                        'isEmpty' => true,
                    )
                ),
                'param' => new ParameterValue(
                    array(
                        'identifier' => 'param',
                        'parameter' => $query->getQueryType()->getParameters()['param'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'value',
                        'isEmpty' => false,
                    )
                ),
            ),
            $query->getParameters()
        );
    }
}
