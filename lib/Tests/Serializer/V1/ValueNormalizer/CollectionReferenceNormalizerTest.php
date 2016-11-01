<?php

namespace Netgen\BlockManager\Tests\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;

class CollectionReferenceNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new CollectionReferenceNormalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer::normalize
     */
    public function testNormalize()
    {
        $collection = new Collection(
            array(
                'id' => 24,
                'type' => Collection::TYPE_MANUAL,
                'shared' => false,
                'name' => null,
                'status' => Value::STATUS_PUBLISHED,
            )
        );

        $collectionReference = new CollectionReference(
            array(
                'block' => new Block(array('id' => 42)),
                'collection' => $collection,
                'identifier' => 'default',
                'offset' => 10,
                'limit' => 5,
            )
        );

        $this->assertEquals(
            array(
                'identifier' => $collectionReference->getIdentifier(),
                'block_id' => $collectionReference->getBlock()->getId(),
                'collection_id' => $collection->getId(),
                'collection_type' => $collection->getType(),
                'collection_shared' => $collection->isShared(),
                'collection_name' => $collection->getName(),
                'offset' => $collectionReference->getOffset(),
                'limit' => $collectionReference->getLimit(),
            ),
            $this->normalizer->normalize(new VersionedValue($collectionReference, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer::normalize
     */
    public function testNormalizeDraft()
    {
        $collection = new Collection(
            array(
                'id' => 24,
                'type' => Collection::TYPE_MANUAL,
                'shared' => false,
                'name' => null,
                'status' => Value::STATUS_DRAFT,
            )
        );

        $collectionReference = new CollectionReference(
            array(
                'block' => new Block(array('id' => 42)),
                'collection' => $collection,
                'identifier' => 'default',
                'offset' => 10,
                'limit' => 5,
            )
        );

        $this->assertEquals(
            array(
                'identifier' => $collectionReference->getIdentifier(),
                'block_id' => $collectionReference->getBlock()->getId(),
                'collection_id' => $collection->getId(),
                'collection_type' => $collection->getType(),
                'collection_shared' => $collection->isShared(),
                'collection_name' => $collection->getName(),
                'offset' => $collectionReference->getOffset(),
                'limit' => $collectionReference->getLimit(),
            ),
            $this->normalizer->normalize(new VersionedValue($collectionReference, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\V1\ValueNormalizer\CollectionReferenceNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $this->assertEquals($expected, $this->normalizer->supportsNormalization($data));
    }

    /**
     * Provider for {@link self::testSupportsNormalization}.
     *
     * @return array
     */
    public function supportsNormalizationProvider()
    {
        return array(
            array(null, false),
            array(true, false),
            array(false, false),
            array('block', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new CollectionReference(), false),
            array(new VersionedValue(new Value(), 1), false),
            array(new VersionedValue(new CollectionReference(), 2), false),
            array(new VersionedValue(new CollectionReference(), 1), true),
        );
    }
}
