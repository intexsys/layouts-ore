<?php

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Value;

final class LayoutUpdateStruct extends Value
{
    /**
     * New layout name.
     *
     * @var string
     */
    public $name;

    /**
     * Modification date of the layout.
     *
     * @var int
     */
    public $modified;

    /**
     * New human readable description of the layout.
     *
     * @var string
     */
    public $description;
}
