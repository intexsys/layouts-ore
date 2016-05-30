<?php

namespace Netgen\BlockManager\View;

interface ItemViewInterface extends ViewInterface
{
    /**
     * Returns the item.
     *
     * @return \Netgen\BlockManager\Item\Item
     */
    public function getItem();

    /**
     * Returns the view type.
     *
     * @return string
     */
    public function getViewType();
}
