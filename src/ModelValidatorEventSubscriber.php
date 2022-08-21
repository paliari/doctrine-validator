<?php

namespace Paliari\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ModelValidatorEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [Events::postLoad, Events::prePersist, Events::postPersist, Events::preRemove, Events::preUpdate];
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        $this->onEvent($event, Validator::UPDATE, false);
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $this->onEvent($event, Validator::CREATE, true);
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->onEvent($event, Validator::UPDATE, false);
    }

    public function preRemove(LifecycleEventArgs $event)
    {
        $this->onEvent($event, Validator::REMOVE, true);
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->onEvent($event, Validator::UPDATE, true);
    }

    protected function onEvent(LifecycleEventArgs $event, string $recordState, bool $validate)
    {
        /** @var ModelValidatorInterface $model */
        $model = $event->getObject();
        $model->setRecordState($recordState);
        if ($validate) {
            $model->validate();
        }
    }
}
