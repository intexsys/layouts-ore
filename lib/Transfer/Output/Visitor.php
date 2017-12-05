<?php

namespace Netgen\BlockManager\Transfer\Output;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Exception\RuntimeException;

/**
 * Visits value objects into hash representation.
 *
 * Hash format is either a scalar value, a hash array (associative array),
 * a pure numeric array or a nested combination of these.
 *
 * @see \Netgen\BlockManager\Transfer\Serializer
 */
abstract class Visitor
{
    /**
     * Check if the visitor accepts the given $value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    abstract public function accept($value);

    /**
     * Visit the given $value into hash representation.
     *
     * @param mixed $value
     * @param \Netgen\BlockManager\Transfer\Output\Visitor|null $subVisitor
     *
     * @return mixed
     */
    abstract public function visit($value, self $subVisitor = null);

    /**
     * Return status string representation for the given $layout.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If status is not recognized
     *
     * @return string
     */
    protected function getStatusString(Value $value)
    {
        switch ($value->getStatus()) {
            case Value::STATUS_DRAFT:
                return 'DRAFT';
            case Value::STATUS_PUBLISHED:
                return 'PUBLISHED';
            case Value::STATUS_ARCHIVED:
                return 'ARCHIVED';
        }

        $statusString = var_export($value->getStatus(), true);

        throw new RuntimeException(sprintf("Unknown status '%s'", $statusString));
    }
}