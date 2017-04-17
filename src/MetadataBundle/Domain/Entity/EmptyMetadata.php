<?php declare(strict_types=1);

namespace MetadataBundle\Domain\Entity;

/**
 * Null object implementation
 */
class EmptyMetadata extends BaseMetadata
{
    public function getType(): string
    {
        return 'empty';
    }
}
