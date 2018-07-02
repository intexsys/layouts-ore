<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType\ItemLink;

use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\NullCmsItem;

final class RemoteIdConverter
{
    private const NULL_LINK = 'null://0';

    /**
     * @var \Netgen\BlockManager\Item\CmsItemLoaderInterface
     */
    private $cmsItemLoader;

    public function __construct(CmsItemLoaderInterface $cmsItemLoader)
    {
        $this->cmsItemLoader = $cmsItemLoader;
    }

    /**
     * Converts the value_type://value format of the item reference to value_type://remote_id.
     * This is useful for various export/import operations between different systems.
     *
     * If the conversion cannot be done, (for example, because item does not exist), a reference to
     * the so called null item will be returned.
     */
    public function convertToRemoteId(string $link): string
    {
        $link = parse_url($link);

        if (!is_array($link) || !isset($link['host'], $link['scheme'])) {
            return self::NULL_LINK;
        }

        $item = $this->cmsItemLoader->load($link['host'], str_replace('-', '_', $link['scheme']));
        if ($item instanceof NullCmsItem) {
            return self::NULL_LINK;
        }

        return $link['scheme'] . '://' . $item->getRemoteId();
    }

    /**
     * Converts the value_type://remote_id format of the item reference to value_type://value.
     * This is useful for various export/import operations between different systems.
     *
     * If the conversion cannot be done, (for example, because item does not exist), a reference to
     * the so called null item will be returned.
     */
    public function convertFromRemoteId(string $link): string
    {
        $link = parse_url($link);

        if (!is_array($link) || !isset($link['host'], $link['scheme'])) {
            return self::NULL_LINK;
        }

        $item = $this->cmsItemLoader->loadByRemoteId($link['host'], str_replace('-', '_', $link['scheme']));
        if ($item instanceof NullCmsItem) {
            return self::NULL_LINK;
        }

        return $link['scheme'] . '://' . $item->getValue();
    }
}
