<?php

namespace Netgen\BlockManager\Configuration\BlockType;

class BlockType
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $definitionIdentifier;

    /**
     * @var array
     */
    protected $defaults = array();

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param bool $enabled
     * @param string $name
     * @param string $definitionIdentifier
     * @param array $defaults
     */
    public function __construct($identifier, $enabled, $name, $definitionIdentifier, array $defaults = array())
    {
        $this->identifier = $identifier;
        $this->enabled = $enabled;
        $this->name = $name;
        $this->definitionIdentifier = $definitionIdentifier;
        $this->defaults = $defaults;
    }

    /**
     * Returns the block type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns if the block type is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns the block type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the definition identifier.
     *
     * @return string
     */
    public function getDefinitionIdentifier()
    {
        return $this->definitionIdentifier;
    }

    /**
     * Returns the default block values.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Returns the default block name.
     *
     * @return string
     */
    public function getDefaultBlockName()
    {
        return isset($this->defaults['name']) ? $this->defaults['name'] : '';
    }

    /**
     * Returns the default block view type.
     *
     * @return string
     */
    public function getDefaultBlockViewType()
    {
        return isset($this->defaults['view_type']) ? $this->defaults['view_type'] : '';
    }

    /**
     * Returns the default block item view type.
     *
     * @return string
     */
    public function getDefaultBlockItemViewType()
    {
        return isset($this->defaults['item_view_type']) ? $this->defaults['item_view_type'] : '';
    }

    /**
     * Returns the default block parameters.
     *
     * @return array
     */
    public function getDefaultBlockParameters()
    {
        return isset($this->defaults['parameters']) ? $this->defaults['parameters'] : array();
    }
}
