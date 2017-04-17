<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Service;

use MetadataCollectionsBundle\Entity\CollectionDefinition;
use MetadataBundle\Domain\Entity\MetadataInterface;

class RouteGenerator
{
    /**
     * Generate an url basing on definition of collection
     *
     * @param CollectionDefinition $definition
     * @param MetadataInterface $metadata
     * @return string
     */
    public function generateUrl(CollectionDefinition $definition, MetadataInterface $metadata): string
    {
        if (strlen($metadata->getUrl()) > 0) {
            return $metadata->getUrl();
        }

        preg_match($definition->getExpression(), basename($metadata->getFilePath()), $matches);
        $url = $definition->getAlias();

        foreach ($matches as $matchId => $match) {
            $url = str_replace('$' . $matchId, $match, $url);
        }

        return $url;
    }
}
