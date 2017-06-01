<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\Serializer\SerializerAwareTrait;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

class PlaceholderNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\Values\VersionedValue $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\API\Values\Block\Placeholder $placeholder */
        $placeholder = $object->getValue();

        $blocks = array();
        foreach ($placeholder as $block) {
            $blocks[] = new View($block, $object->getVersion());
        }

        return array(
            'identifier' => $placeholder->getIdentifier(),
            'blocks' => $this->serializer->normalize($blocks, $format, $context),
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
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Placeholder && $data->getVersion() === Version::API_V1;
    }
}