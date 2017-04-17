<?php declare(strict_types=1);

namespace WikiBundle\Domain\Event;

use Symfony\Component\EventDispatcher\Event;
use WikiBundle\Domain\Context\AbstractContext;
use WikiBundle\Domain\Context\FileProcessContext;

class FilePostCompileEvent extends FileProcessEvent
{
    /**
     * @return FileProcessContext
     */
    public function getContext(): AbstractContext
    {
        return $this->context;
    }
}
