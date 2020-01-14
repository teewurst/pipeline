<?php

declare(strict_types=1);

namespace teewurst\Pipeline;

use Psr\Container\ContainerInterface;

/**
 * Class PipelineService
 *
 * Create Pipeline by
 * @package teewurst\Pipeline
 * @author Martin Ruf <Martin.Ruf@check24.de>
 */
class PipelineService
{

    /**
     * Creates Pipeline by array and configuration
     *
     * @param array  $tasks
     * @param object $options
     *
     * @return PipelineInterface
     */
    public function create(array $tasks, object $options = null): PipelineInterface
    {
        $pipeline = new Pipeline($this->createRecursive($tasks));
        $pipeline->setOptions($options ?? new \stdClass());

        return $pipeline;
    }

    /**
     * Uses a psr-11 Container (=Zend ServiceManager, =Laravel Serivemanager etc) to create all tasks
     *
     * @param ContainerInterface $serviceContainer
     * @param array              $tasks
     * @param object|null        $options
     *
     * @return PipelineInterface
     */
    public function createPsr11(ContainerInterface $serviceContainer, array $tasks, object $options = null)
    {
        array_walk_recursive(
            $tasks,
            function (&$value) use ($serviceContainer) {
                if (is_string($value)) {
                    $value = $serviceContainer->get($value);
                }
            }
        );

        return $this->create($tasks, $options ?? new \stdClass());
    }

    /**
     * Recursively transform task array to valid pipeline
     *
     * @param array $tasks
     * @return array
     */
    private function createRecursive(array $tasks): array
    {
        foreach ($tasks as &$task) {
            if (is_array($task)) {
                $task = new RecursivePipeline($this->createRecursive($task));
            }
            if (!$task instanceof TaskInterface) {
                throw new \InvalidArgumentException('A task does not implement teewurst\Pipeline\TaskInterface');
            }
        }

        return $tasks;
    }

}
