<?php

namespace Netgen\BlockManager\Collection\Registry;

use Netgen\BlockManager\Collection\QueryTypeInterface;

interface QueryTypeRegistryInterface
{
    /**
     * Returns a query type.
     *
     * @param string $type
     *
     * @throws \InvalidArgumentException If query type does not exist
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function getQueryType($type);

    /**
     * Adds a query type to registry.
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     */
    public function addQueryType(QueryTypeInterface $queryType);

    /**
     * Returns all query types.
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface[]
     */
    public function getQueryTypes();

    /**
     * Returns if registry has a query type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasQueryType($type);
}
