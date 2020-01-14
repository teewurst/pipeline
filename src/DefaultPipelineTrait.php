<?php
declare(strict_types=1);

namespace teewurst\Pipeline;

/**
 * Trait DefaultPipelineTrait
 *
 * Just some basic functions for Pipelines
 *
 * @package teewurst\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
trait DefaultPipelineTrait
{
    /** @var TaskInterface[] */
    private $tasks;
    /** @var object */
    private $options;

    /**
     * DefaultPipelineTrait constructor.
     *
     * @param TaskInterface[] $tasks Array of tasks
     */
    public function __construct(array $tasks = [])
    {
        $this->tasks = $tasks;
    }

    /**
     * Adds a new Task to the pipeline
     *
     * @param TaskInterface $task Task to be added
     *
     * @return void
     */
    public function add(TaskInterface $task): void
    {
        $this->tasks[] = $task;
    }

    /**
     * Shifts the current task from the pipeline, and removes it from execution
     *
     * @return TaskInterface|null
     */
    public function next(): ?TaskInterface
    {
        return array_shift($this->tasks);
    }

    /**
     * Set Config for your pipeline, which is accessible from your tasks
     *
     * @param object $options
     *
     * @return void
     */
    public function setOptions(object $options): void
    {
        $this->options = $options;
    }

    /**
     * Returns configuration for your pipeline (exp env variables?)
     *
     * @return object
     */
    public function getOptions(): object
    {
        return $this->options;
    }
}
