<?php declare(strict_types=1);

namespace WikiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;

class WikiExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/'));
        $loader->load('services/services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->prepareFetcher($container, $config);
        $this->preparePayloadFactory($container);
        $this->prepareStorageManager($container, $config);
    }

    private function prepareFetcher(ContainerBuilder $container, array $config)
    {
        $fetcherService = $container->findDefinition('wolnosciowiec.wiki.fetcher');
        $fetcherImplementations = $container->findTaggedServiceIds('wolnosciowiec.wiki.fetchers');

        foreach ($fetcherImplementations as $id => $tags) {
            $fetcherService->addMethodCall('addFetcher', [new Reference($id)]);
        }
    }

    private function prepareStorageManager(ContainerBuilder $container, array $config)
    {
        $fetcherService = $container->findDefinition('wolnosciowiec.wiki.manager.storage');
        $knownRepositories = [];

        foreach ($config['repositories'] as $name => $details) {
            $knownRepositories[$name] = $details['address'] . '@' . $details['branch'];
        }

        $fetcherService->addMethodCall('setKnownRepositories', [$knownRepositories]);
    }

    private function preparePayloadFactory(ContainerBuilder $container)
    {
        $factory = $container->findDefinition('wolnosciowiec.wiki.factory.payload');
        $implementations = $container->findTaggedServiceIds('wolnosciowiec.wiki.factory.payload');

        foreach ($implementations as $id => $tags) {
            $factory->addMethodCall('addFactory', [new Reference($id)]);
        }
    }
}
