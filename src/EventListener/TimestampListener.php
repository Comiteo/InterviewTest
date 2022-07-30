<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use DateTime;

class TimestampListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        $entity
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());
    }
}