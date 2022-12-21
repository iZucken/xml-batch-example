<?php

namespace App\DataDefinitions\Runners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Wvision\Bundle\DataDefinitionsBundle\Context\RunnerContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Event\ImportDefinitionEvent;
use Wvision\Bundle\DataDefinitionsBundle\Runner\RunnerInterface;

class ReportingRunner implements RunnerInterface, EventSubscriberInterface
{
    private \SplObjectStorage $collectorMap;
    private array $stats = [];
    private array $seen = [];

    public function __construct()
    {
        $this->collectorMap = new \SplObjectStorage;
    }

    public function preRun(RunnerContextInterface $context)
    {
        $object = $context->getObject();
        $this->stats[$object->getClassName()] = $this->stats[$object->getClassName()] ?? [
            'discovered' => [],
            'created' => [],
            'updated' => [],
        ];
        if (!$object->getId() || isset($this->seen[$object->getId()])) {
            return;
        }
        $this->collectorMap[$object] = $object->getId() === null;
        $this->stats[$object->getClassName()]['discovered']++;
    }

    public function postRun(RunnerContextInterface $context)
    {
        $object = $context->getObject();
        if (isset($this->collectorMap[$object])) {
            if ($this->collectorMap[$object]) {
                $this->stats[$object->getClassName()]['created']++;
            } else {
                $this->stats[$object->getClassName()]['updated']++;
            }
            unset($this->collectorMap[$object]);
            $this->seen[$object->getId()] = true;
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'data_definitions.stop' => 'onFinishImport',
            'data_definitions.import.finished' => 'onFinishImport',
        ];
    }

    public function onFinishImport(ImportDefinitionEvent $event)
    {
        // todo: dispatch report message/event
        $this->seen = [];
        $this->collectorMap = new \SplObjectStorage;
        $this->stats = [];
    }
}
