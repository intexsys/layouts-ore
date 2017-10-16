<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;

final class ResultBuilder implements ResultBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultIteratorFactory
     */
    private $resultIteratorFactory;

    /**
     * @var \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory
     */
    private $collectionIteratorFactory;

    public function __construct(
        ResultIteratorFactory $resultIteratorFactory,
        CollectionIteratorFactory $collectionIteratorFactory
    ) {
        $this->resultIteratorFactory = $resultIteratorFactory;
        $this->collectionIteratorFactory = $collectionIteratorFactory;
    }

    public function build(Collection $collection, $offset = 0, $limit = null, $flags = 0)
    {
        $collectionIterator = $this->collectionIteratorFactory->getCollectionIterator(
            $collection,
            $flags
        );

        $results = $this->resultIteratorFactory->getResultIterator(
            $collectionIterator,
            $offset,
            $limit,
            $flags
        );

        return new ResultSet(
            array(
                'collection' => $collection,
                'results' => array_values(
                    iterator_to_array($results)
                ),
                'totalCount' => $collectionIterator->count(),
                'offset' => $offset,
                'limit' => $limit,
            )
        );
    }
}