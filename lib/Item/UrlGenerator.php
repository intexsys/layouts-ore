<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

use Netgen\Layouts\Exception\Item\ItemException;
use Psr\Container\ContainerInterface;

final class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $valueUrlGenerators;

    public function __construct(ContainerInterface $valueUrlGenerators)
    {
        $this->valueUrlGenerators = $valueUrlGenerators;
    }

    public function generate(CmsItemInterface $item): string
    {
        $object = $item->getObject();
        if ($item instanceof NullCmsItem || $object === null) {
            return '';
        }

        $valueUrlGenerator = $this->getValueUrlGenerator($item->getValueType());

        return $valueUrlGenerator->generate($object) ?? '';
    }

    /**
     * Returns the value URL generator for provided value type from the collection.
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException If the value URL generator does not exist or is not of correct type
     */
    private function getValueUrlGenerator(string $valueType): ValueUrlGeneratorInterface
    {
        if (!$this->valueUrlGenerators->has($valueType)) {
            throw ItemException::noValueType($valueType);
        }

        $valueUrlGenerator = $this->valueUrlGenerators->get($valueType);
        if (!$valueUrlGenerator instanceof ValueUrlGeneratorInterface) {
            throw ItemException::noValueType($valueType);
        }

        return $valueUrlGenerator;
    }
}
