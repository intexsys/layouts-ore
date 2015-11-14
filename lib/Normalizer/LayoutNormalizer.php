<?php

namespace Netgen\BlockManager\Normalizer;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutNormalizer implements NormalizerInterface
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return array(
            'id' => $object->getId(),
            'parent_id' => $object->getParentId(),
            'identifier' => $object->getIdentifier(),
            'created_at' => $object->getCreated(),
            'updated_at' => $object->getModified(),
            'title' => $this->configuration->getParameter('layouts')[$object->getIdentifier()]['name'],
        );
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data
     * @param string $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Layout;
    }
}
