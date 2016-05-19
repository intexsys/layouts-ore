<?php

namespace Netgen\BlockManager\API\Values\Page;

interface Layout
{
    /**
     * @const string
     */
    const STATUS_DRAFT = 0;

    /**
     * @const string
     */
    const STATUS_PUBLISHED = 1;

    /**
     * @const string
     */
    const STATUS_ARCHIVED = 2;

    /**
     * @const string
     */
    const STATUS_TEMPORARY_DRAFT = 3;

    /**
     * Returns the layout ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the parent layout ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Returns the layout type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the layout human readable name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns when was the layout created.
     *
     * @return \DateTime
     */
    public function getCreated();

    /**
     * Returns when was the layout last updated.
     *
     * @return \DateTime
     */
    public function getModified();

    /**
     * Returns the status of the layout.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns all zones from the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone[]
     */
    public function getZones();

    /**
     * Returns the specified zone or null if zone does not exist.
     *
     * @param string $zoneIdentifier
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function getZone($zoneIdentifier);

    /**
     * Returns if layout has a specified zone.
     *
     * @param string $zoneIdentifier
     *
     * @return bool
     */
    public function hasZone($zoneIdentifier);
}
