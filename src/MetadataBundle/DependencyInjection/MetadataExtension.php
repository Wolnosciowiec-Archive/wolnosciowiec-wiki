<?php declare(strict_types=1);

namespace MetadataBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;

class MetadataExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/'));
        $loader->load('services.yml');

        $this->prepareMetadataFactory($container);
    }

    /**
     * @see WikiBundle\Factory\Metadata\MetadataFactory::addMetadata()
     */
    private function prepareMetadataFactory(ContainerBuilder $containerBuilder)
    {
        $factory = $containerBuilder->findDefinition('wolnosciowiec.wiki.factory.metadata');
        $metadatas = $containerBuilder->findTaggedServiceIds('metadata');

        foreach ($metadatas as $id => $metadata) {
            $factory->addMethodCall('addMetadata', [new Reference($id)]);
        }
    }
}
