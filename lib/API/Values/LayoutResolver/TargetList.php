<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;

final class TargetList extends ArrayCollection
{
    public function __construct(array $targets = [])
    {
        parent::__construct(
            array_filter(
                $targets,
                static function (Target $target): bool {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\Layouts\API\Values\LayoutResolver\Target[]
     */
    public function getTargets(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getTargetIds(): array
    {
        return array_map(
            static function (Target $target) {
                return $target->getId();
            },
            $this->getTargets()
        );
    }
}
