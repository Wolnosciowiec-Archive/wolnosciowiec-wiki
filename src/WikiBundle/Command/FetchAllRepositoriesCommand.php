<?php declare(strict_types=1);

namespace WikiBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WikiBundle\Domain\Service\RepositoryProvider\RepositoryProviderInterface;
use WikiBundle\Service\PayloadHandler\PayloadHandlerService;

class FetchAllRepositoriesCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        return $this;
    }

    protected function configure()
    {
        $this->setName('repositories:fetch')
            ->setDescription('Updates all repositories defined in wiki.yml');
    }

    /**
     * @return RepositoryProviderInterface
     */
    private function getManager()
    {
        return $this->container->get('wolnosciowiec.wiki.provider.repository');
    }

    /**
     * @return PayloadHandlerService
     */
    private function getService()
    {
        return $this->container->get('wolnosciowiec.wiki.handler.payload');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repositories = $this->getManager()->getIndexedByAddress();

        foreach ($repositories as $repositoryDefinition) {
            $output->writeln('<info>==></info> <comment>Processing ' . $repositoryDefinition->getAddress() .
                             ' @ ' . $repositoryDefinition->getBranch() . '</comment>');

            $this->getService()->handlePayload(
                $repositoryDefinition->createPayload(),
                $repositoryDefinition->getFetcher()
            );
        }
    }
}