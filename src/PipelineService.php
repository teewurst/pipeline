<?php

declare(strict_types=1);

namespace teewurst\Pipeline;

use Psr\Container\ContainerInterface;
use teewurst\Pipeline\Pipeline;

/**
 * Class PipelineService
 *
 * @template T
 * @template P of \teewurst\Pipeline\PipelineInterface
 * Create Pipeline by config array
 * @package teewurst\Pipeline
 * @author Martin Ruf <Martin.Ruf@check24.de>
 */
class PipelineService
{

    /**
     * Creates Pipeline by array and configuration
     *
     * @param array<TaskInterface<T>|array<TaskInterface<T>>> $tasks
     * @param class-string<P> $classFqn
     * @param ?array<mixed> $options
     *
     * @return P
     */
    public function create(array $tasks, string $classFqn = Pipeline::class, array $options = null): PipelineInterface
    {
        $interfaces = class_implements($classFqn);
        if (!class_exists($classFqn) || !$interfaces || !in_array(PipelineInterface::class, $interfaces, true)) {
            throw new \RuntimeException("$classFqn does not implement \\teewurst\\Pipeline\\PipelineInterface");
        }

        $pipeline = new $classFqn($this->createRecursive($tasks));
        $pipeline->setOptions($options ?? []);

        return $pipeline;
    }

    /**
     * Uses a psr-11 Container (=Zend ServiceManager, =Laravel Serivemanager etc) to create all tasks
     *
     * @param ContainerInterface $serviceContainer
     * @param array<class-string<TaskInterface<T>>|TaskInterface<T>|array<class-string<TaskInterface<T>>|TaskInterface<T>>> $tasks
     * @param class-string<P> $classFqn
     * @param array<mixed>|null $options
     *
     * @return P
     */
    public function createPsr11(ContainerInterface $serviceContainer, array $tasks, string $classFqn = Pipeline::class, array $options = null): PipelineInterface
    {
        array_walk_recursive(
            $tasks,
            static function (&$value) use ($serviceContainer) {
                if (is_string($value)) {
                    $value = $serviceContainer->get($value);
                }
            }
        );
        /** @var array<TaskInterface<T>|array<TaskInterface<T>>> $tasks php stan does not understand this replaces the strings by its class equivalent */
        return $this->create($tasks, $classFqn, $options ?? []);
    }

    /**
     * Recursively transform task array to valid pipeline
     *
     * @param array<TaskInterface<T>|array<TaskInterface<T>>> $tasks
     * @return array<TaskInterface<T>>
     */
    private function createRecursive(array $tasks): array
    {
        foreach ($tasks as $i => $task) {
            if (is_array($task)) {
                $task = new RecursivePipeline($this->createRecursive($task));
            }
            if (!$task instanceof TaskInterface) {
                throw new \InvalidArgumentException('A task does not implement teewurst\Pipeline\TaskInterface');
            }
            $tasks[$i] = $task;
        }
        /** @var array<TaskInterface<T>> $tasks */
        return $tasks;
    }

}
