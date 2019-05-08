<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Parameters\ParameterTypeInterface;
use Traversable;

final class ParameterTypeRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var \Netgen\Layouts\Parameters\ParameterTypeInterface[]
     */
    private $parameterTypes = [];

    /**
     * @var \Netgen\Layouts\Parameters\ParameterTypeInterface[]
     */
    private $parameterTypesByClass = [];

    public function __construct(iterable $parameterTypes)
    {
        foreach ($parameterTypes as $parameterType) {
            if ($parameterType instanceof ParameterTypeInterface) {
                $this->parameterTypes[$parameterType::getIdentifier()] = $parameterType;
                $this->parameterTypesByClass[get_class($parameterType)] = $parameterType;
            }
        }
    }

    /**
     * Returns if registry has a parameter type.
     */
    public function hasParameterType(string $identifier): bool
    {
        return array_key_exists($identifier, $this->parameterTypes);
    }

    /**
     * Returns a parameter type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterTypeException If parameter type does not exist
     */
    public function getParameterType(string $identifier): ParameterTypeInterface
    {
        if (!$this->hasParameterType($identifier)) {
            throw ParameterTypeException::noParameterType($identifier);
        }

        return $this->parameterTypes[$identifier];
    }

    /**
     * Returns a parameter type with provided class.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterTypeException If parameter type does not exist
     */
    public function getParameterTypeByClass(string $class): ParameterTypeInterface
    {
        if (!array_key_exists($class, $this->parameterTypesByClass)) {
            throw ParameterTypeException::noParameterTypeClass($class);
        }

        return $this->parameterTypesByClass[$class];
    }

    /**
     * Returns all parameter types.
     *
     * @return \Netgen\Layouts\Parameters\ParameterTypeInterface[]
     */
    public function getParameterTypes(): array
    {
        return $this->parameterTypes;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->parameterTypes);
    }

    public function count(): int
    {
        return count($this->parameterTypes);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasParameterType($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getParameterType($offset);
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
