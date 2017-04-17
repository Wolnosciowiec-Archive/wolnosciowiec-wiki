<?php declare(strict_types=1);

namespace WikiBundle\Domain\Event;

use Symfony\Component\EventDispatcher\Event;
use WikiBundle\Domain\Context\RoutingPostCollectionContext;

class RouterPostCollectEvent extends Event
{
    /**
     * @var RoutingPostCollectionContext $context
     */
    protected $context;

    public function __construct(RoutingPostCollectionContext $context)
    {
        $this->context = $context;
    }

    public function getContext(): RoutingPostCollectionContext
    {
        return $this->context;
    }
}
