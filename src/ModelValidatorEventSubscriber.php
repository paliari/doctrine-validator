<?php

namespace Paliari\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ModelValidatorEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
            Events::prePersist,
            Events::postPersist,
            Events::preRemove,
            Events::postRemove,
            Events::preUpdate,
            Events::postUpdate,
        ];
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
        $this->onEvent($event, Validator::UPDATE, false, true);
    }

    public function preRemove(LifecycleEventArgs $event)
    {
        $this->onEvent($event, Validator::REMOVE, true);
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        $this->onEvent($event, Validator::REMOVE, false, true);
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->onEvent($event, Validator::UPDATE, true);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->onEvent($event, Validator::UPDATE, false, true);
    }

    protected function onEvent(LifecycleEventArgs $event, string $recordState, bool $validate, bool $resetErrors = false)
    {
        /** @var ModelValidatorInterface $model */
        $model = $event->getObject();
        if (!method_exists($model, 'setRecordState') || !method_exists($model, 'validate')) {
            return;
        }
        $model->setRecordState($recordState);
        if ($resetErrors) {
            $model->errors = null;
        }
        if ($validate) {
            $model->validate();
        }
    }
}
