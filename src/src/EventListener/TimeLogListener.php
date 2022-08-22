<?php

namespace App\EventListener;

use \Doctrine\ORM\Event\LifecycleEventArgs;
use \App\Entity\TimeLogInterface;

class TimeLogListener
{

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if( $entity instanceof TimeLogInterface){
            $entity->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if( $entity instanceof TimeLogInterface){
            $entity->setUpdatedAt(new \DateTimeImmutable());
        }
    }
}