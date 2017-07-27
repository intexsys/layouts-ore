<?php

namespace Netgen\BlockManager\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinitionInterface;

class BlockStructBuilder
{
    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder
     */
    protected $configStructBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder $configStructBuilder
     */
    public function __construct(ConfigStructBuilder $configStructBuilder)
    {
        $this->configStructBuilder = $configStructBuilder;
    }

    /**
     * Creates a new block create struct.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockCreateStruct
     */
    public function newBlockCreateStruct(BlockDefinitionInterface $blockDefinition)
    {
        $config = $blockDefinition->getConfig();

        $viewTypeIdentifier = $config->getViewTypeIdentifiers()[0];
        $viewType = $config->getViewType($viewTypeIdentifier);
        $itemViewTypeIdentifier = $viewType->getItemViewTypeIdentifiers()[0];

        $blockCreateStruct = new BlockCreateStruct(
            array(
                'definition' => $blockDefinition,
                'viewType' => $viewTypeIdentifier,
                'itemViewType' => $itemViewTypeIdentifier,
                'isTranslatable' => $config->isTranslatable(),
                'alwaysAvailable' => true,
            )
        );

        $blockCreateStruct->fill($blockDefinition);

        return $blockCreateStruct;
    }

    /**
     * Creates a new block update struct.
     *
     * @param string $locale
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct
     */
    public function newBlockUpdateStruct($locale, Block $block = null)
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->locale = $locale;

        if (!$block instanceof Block) {
            return $blockUpdateStruct;
        }

        $blockUpdateStruct->viewType = $block->getViewType();
        $blockUpdateStruct->itemViewType = $block->getItemViewType();
        $blockUpdateStruct->name = $block->getName();
        $blockUpdateStruct->alwaysAvailable = $block->isAlwaysAvailable();
        $blockUpdateStruct->fillFromValue($block->getDefinition(), $block);

        $this->configStructBuilder->buildConfigUpdateStructs($block, $blockUpdateStruct);

        return $blockUpdateStruct;
    }
}
