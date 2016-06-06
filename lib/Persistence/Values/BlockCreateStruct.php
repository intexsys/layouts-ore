<?php

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\ValueObject;

class BlockCreateStruct extends ValueObject
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var string
     */
    public $zoneIdentifier;

    /**
     * @var int
     */
    public $status;

    /**
     * @var int
     */
    public $position;

    /**
     * @var string
     */
    public $definitionIdentifier;

    /**
     * @var string
     */
    public $viewType;

    /**
     * @var string
     */
    public $itemViewType;

    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $parameters;
}
