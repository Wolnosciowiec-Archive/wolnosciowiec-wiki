<?php declare(strict_types=1);

namespace WikiBundle\Domain\Event;

use Symfony\Component\EventDispatcher\Event;
use WikiBundle\Domain\Context\FileProcessContext;
use WikiBundle\Domain\Processor\FileProcessorInterface;

abstract class FileProcessEvent extends Event
{
    /** @var FileProcessContext $context */
    protected $context;

    /** @var FileProcessorInterface $processor */
    protected $processor;

    public function __construct(FileProcessContext $context, FileProcessorInterface $processor)
    {
        $this->context   = $context;
        $this->processor = $processor;
    }

    public function getContext(): FileProcessContext
    {
        return $this->context;
    }

    public function getProcessor(): FileProcessorInterface
    {
        return $this->processor;
    }
}
