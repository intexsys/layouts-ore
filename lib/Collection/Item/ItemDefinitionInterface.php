<?php

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\Config\ConfigDefinitionAwareInterface;

/**
 * Item definition represents the model of the collection item.
 * Currently, this only specifies what kind of configuration the item accepts.
 */
interface ItemDefinitionInterface extends ConfigDefinitionAwareInterface
{
    /**
     * Returns the value type for this definition.
     *
     * @return string
     */
    public function getValueType();

    /**
     * Returns the available config definitions.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions();
}