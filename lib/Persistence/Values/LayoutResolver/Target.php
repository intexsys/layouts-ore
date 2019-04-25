<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class Target extends Value
{
    use HydratorTrait;

    /**
     * Target ID.
     *
     * @var int
     */
    public $id;

    /**
     * Target UUID.
     *
     * @var string
     */
    public $uuid;

    /**
     * ID of the rule where this target is located.
     *
     * @var int
     */
    public $ruleId;

    /**
     * UUID of the rule where this target is located.
     *
     * @var string
     */
    public $ruleUuid;

    /**
     * Identifier of the target type.
     *
     * @var string
     */
    public $type;

    /**
     * Target value.
     *
     * @var mixed
     */
    public $value;
}
