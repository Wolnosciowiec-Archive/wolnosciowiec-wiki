<?php declare(strict_types=1);

namespace WikiBundle\Service\PayloadHandler;

use Psr\Log\LoggerInterface;
use WikiBundle\Domain\Entity\Payload;
use WikiBundle\Domain\Factory\Payload\PayloadFactoryInterface;
use WikiBundle\Domain\Processor\FileProcessorInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;
use WikiBundle\Exception\PushFailedException;
use WikiBundle\Service\Fetcher\FetcherService;

class PayloadHandlerService
{
    /**
     * @var FetcherService $fetcherService
     */
    private $fetcherService;

    /**
     * @var PayloadFactoryInterface $payloadFactory
     */
    private $payloadFactory;

    /**
     * @var StorageManagerInterface $storageManager
     */
    private $storageManager;

    /**
     * @var FileProcessorInterface $fileProcessor
     */
    private $fileProcessor;

    public function __construct(
        FetcherService $fetcherService,
        PayloadFactoryInterface $payloadFactory,
        StorageManagerInterface $storageManager,
        FileProcessorInterface $fileProcessor,
        LoggerInterface $logger
    ) {
        $this->fetcherService = $fetcherService;
        $this->payloadFactory = $payloadFactory;
        $this->storageManager = $storageManager;
        $this->fileProcessor  = $fileProcessor;
        $this->logger         = $logger;
    }

    /**
     * @param Payload $payload
     * @param string $fetcherName
     *
     * @throws PushFailedException
     * @return bool
     */
    public function handlePayload(Payload $payload, string $fetcherName)
    {
        try {
            $repositoryName = $this->storageManager->getRepositoryName($payload->getUrl(), $payload->getBranch());
            $path = $this->fetcherService->cloneRepository($fetcherName, $payload->getUrl(), $payload->getBranch());
            $this->fileProcessor->processRepository($path, $repositoryName);

        } catch (\Exception $e) {
            $this->logger->critical(
                'Push failed for ' . $payload->getUrl() . '@' . $payload->getBranch() . ': ' . $e->getMessage());

            throw new PushFailedException(
                'Push failed for ' . $payload->getUrl() . '@' . $payload->getBranch() . ': ' . $e->getMessage(), 0, $e);
        }

        return true;
    }
}
