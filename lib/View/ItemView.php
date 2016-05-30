<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Item\Item;

class ItemView extends View implements ItemViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\Item $item
     * @param string $viewType
     */
    public function __construct(Item $item, $viewType)
    {
        $this->valueObject = $item;
        $this->internalParameters['item'] = $item;
        $this->internalParameters['viewType'] = $viewType;
    }

    /**
     * Returns the item.
     *
     * @return \Netgen\BlockManager\Item\Item
     */
    public function getItem()
    {
        return $this->valueObject;
    }

    /**
     * Returns the view type.
     *
     * @return string
     */
    public function getViewType()
    {
        return $this->internalParameters['viewType'];
    }

    /**
     * Returns the view alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'item_view';
    }
}
