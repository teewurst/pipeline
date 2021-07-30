<?php
declare(strict_types=1);

namespace teewurst\Pipeline;

/**
 * Class Pipeline
 *
 * Classic pipeline
 *
 * @template T of PayloadInterface
 * @implements PipelineInterface<T>
 *
 * @package teewurst\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class Pipeline implements PipelineInterface
{

    /** @var TaskInterface<T>[] */
    private $tasks;
    /** @var object */
    private $options;

    /**
     * DefaultPipelineTrait constructor.
     *
     * @param TaskInterface<T>[] $tasks Array of tasks
     */
    public function __construct(array $tasks = [])
    {
        $this->tasks = $tasks;
    }

    /**
     * Adds a new Task to the pipeline
     *
     * @param TaskInterface<T> $task Task to be added
     *
     * @return void
     */
    public function add($task): void
    {
        $this->tasks[] = $task;
    }

    /**
     * Shifts the current task from the pipeline, and removes it from execution
     *
     * @return TaskInterface<T>|null
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

    /**
     * Start execution of all tasks within the pipeline
     *
     * @param T $payload Payload to be passed through all tasks
     *
     * @return T
     */
    public function handle($payload)
    {
        $task = $this->next();

        if ($task === null) {
            return $payload;
        }

        return $task($payload, $this);
    }
}
