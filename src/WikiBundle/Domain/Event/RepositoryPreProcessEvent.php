<?php declare(strict_types=1);

namespace WikiBundle\Domain\Event;

use WikiBundle\Domain\Context\AbstractContext;
use WikiBundle\Domain\Context\RepositoryProcessContext;

class RepositoryPreProcessEvent extends FileProcessEvent
{
    /**
     * @return RepositoryProcessContext
     */
    public function getContext(): AbstractContext
    {
        return $this->context;
    }
}
