<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\CollectionValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockCollectionController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\CollectionValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\CollectionValidator $validator
     */
    public function __construct(
        CollectionService $collectionService,
        BlockService $blockService,
        ConfigurationInterface $configuration,
        CollectionValidator $validator
    ) {
        $this->collectionService = $collectionService;
        $this->blockService = $blockService;
        $this->configuration = $configuration;
        $this->validator = $validator;
    }

    /**
     * Loads all block collections.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollections(Block $block)
    {
        $blockCollections = array_map(
            function (CollectionReference $collection) {
                return new VersionedValue($collection, self::API_VERSION);
            },
            $this->blockService->loadBlockCollections($block)
        );

        return new ValueArray($blockCollections);
    }

    /**
     * Loads the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollection(Block $block, Collection $collection)
    {
        return new VersionedValue($collection, self::API_VERSION);
    }

    /**
     * Loads all collection items.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionItems(Block $block, Collection $collection)
    {
        $manualItems = array_map(
            function (Item $item) {
                return new VersionedValue($item, self::API_VERSION);
            },
            array_values($collection->getManualItems())
        );

        $overrideItems = array_map(
            function (Item $item) {
                return new VersionedValue($item, self::API_VERSION);
            },
            array_values($collection->getOverrideItems())
        );

        return new ValueArray(
            array(
                'manual_items' => $manualItems,
                'override_items' => $overrideItems,
            )
        );
    }

    /**
     * Loads all collection queries.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionQueries(Block $block, Collection $collection)
    {
        $queries = array_map(
            function (Query $query) {
                return new VersionedValue($query, self::API_VERSION);
            },
            $collection->getQueries()
        );

        return new ValueArray($queries);
    }

    /**
     * Loads the item.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadItem(Block $block, Collection $collection, Item $item)
    {
        return new VersionedValue($item, self::API_VERSION);
    }

    /**
     * Adds an item inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View If some of the required request parameters are empty, missing or have an invalid format
     *
     */
    public function addItem(Block $block, Collection $collection, Request $request)
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            $request->request->get('type'),
            $request->request->get('value_id'),
            $request->request->get('value_type')
        );

        $createdItem = $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            $request->request->get('position')
        );

        return new VersionedValue($createdItem, self::API_VERSION, Response::HTTP_CREATED);
    }

    /**
     * Moves the item inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response If some of the required request parameters are empty, missing or have an invalid format
     *
     */
    public function moveItem(Block $block, Collection $collection, Item $item, Request $request)
    {
        $this->collectionService->moveItem(
            $item,
            $request->request->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes the item.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteItem(Block $block, Collection $collection, Item $item)
    {
        $this->collectionService->deleteItem($item);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Loads the query.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadQuery(Block $block, Collection $collection, Query $query)
    {
        return new VersionedValue($query, self::API_VERSION);
    }
}
