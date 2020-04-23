<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Item\ValueType\ValueType;
use Traversable;
use function array_filter;
use function count;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Item\ValueType\ValueType>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Item\ValueType\ValueType>
 */
final class ValueTypeRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\Layouts\Item\ValueType\ValueType>
     */
    private $valueTypes;

    /**
     * @param array<string, \Netgen\Layouts\Item\ValueType\ValueType> $valueTypes
     */
    public function __construct(array $valueTypes)
    {
        $this->valueTypes = array_filter(
            $valueTypes,
            static function (ValueType $valueType): bool {
                return true;
            }
        );
    }

    /**
     * Returns if registry has a value type.
     */
    public function hasValueType(string $identifier): bool
    {
        return isset($this->valueTypes[$identifier]);
    }

    /**
     * Returns a value type for provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException If value type does not exist
     */
    public function getValueType(string $identifier): ValueType
    {
        if (!$this->hasValueType($identifier)) {
            throw ItemException::noValueType($identifier);
        }

        return $this->valueTypes[$identifier];
    }

    /**
     * Returns all value types.
     *
     * @param bool $onlyEnabled
     *
     * @return array<string, \Netgen\Layouts\Item\ValueType\ValueType>
     */
    public function getValueTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->valueTypes;
        }

        return array_filter(
            $this->valueTypes,
            static function (ValueType $valueType): bool {
                return $valueType->isEnabled();
            }
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->valueTypes);
    }

    public function count(): int
    {
        return count($this->valueTypes);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasValueType($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): ValueType
    {
        return $this->getValueType($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
