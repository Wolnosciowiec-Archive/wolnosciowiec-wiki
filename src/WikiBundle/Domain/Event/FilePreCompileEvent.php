<?php declare(strict_types=1);

namespace WikiBundle\Domain\Event;

use WikiBundle\Domain\Context\AbstractContext;
use WikiBundle\Domain\Context\FileProcessContext;

class FilePreCompileEvent extends FileProcessEvent
{
    /**
     * @return FileProcessContext
     */
    public function getContext(): AbstractContext
    {
        return $this->context;
    }
}
