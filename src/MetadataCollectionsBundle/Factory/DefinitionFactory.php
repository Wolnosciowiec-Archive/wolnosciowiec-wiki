<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Factory;

use JMS\Serializer\SerializerInterface;
use MetadataCollectionsBundle\Entity\CollectionDefinition;

class DefinitionFactory
{
    /**
     * @var SerializerInterface $serializer
     */
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createDefinitionFrom(array $data): CollectionDefinition
    {
        return $this->serializer->deserialize(json_encode($data), CollectionDefinition::class, 'json');
    }
}
