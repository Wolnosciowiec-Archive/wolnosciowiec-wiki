<?php declare(strict_types=1);

namespace WikiBundle\Domain\Event;

use Symfony\Component\EventDispatcher\Event;
use WikiBundle\Domain\Context\AbstractContext;
use WikiBundle\Domain\Processor\FileProcessorInterface;

abstract class FileProcessEvent extends Event
{
    protected $context;

    /** @var FileProcessorInterface $processor */
    protected $processor;

    public function __construct(AbstractContext $context, FileProcessorInterface $processor)
    {
        $this->context   = $context;
        $this->processor = $processor;
    }

    public function getContext(): AbstractContext
    {
        return $this->context;
    }

    public function getProcessor(): FileProcessorInterface
    {
        return $this->processor;
    }
}
