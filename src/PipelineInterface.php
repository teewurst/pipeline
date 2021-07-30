<?php
declare(strict_types=1);

namespace teewurst\Pipeline;

/**
 * Interface PipelineInterface
 *
 * Contains a certain amount of tasks to be executed
 *
 * @template T of \teewurst\Pipeline\PayloadInterface
 *
 * @package teewurst\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
interface PipelineInterface
{

    /**
     * Adds a new Task to the pipeline
     *
     * @param TaskInterface<T> $task Task to be added
     *
     * @return void
     */
    public function add($task): void;

    /**
     * Shifts the currect task from the pipeline, and removes it from execution
     *
     * @return TaskInterface<T>|null
     */
    public function next(): ?TaskInterface;

    /**
     * Start execution of all tasks within the pipeline
     *
     * @param T $payload Payload to be passed through all tasks
     *
     * @return T
     */
    public function handle($payload);

    /**
     * Set Config for your pipeline, which is accessible from your tasks
     *
     * @param object $options
     *
     * @return void
     */
    public function setOptions(object $options): void;

    /**
     * Returns configuration for your pipeline (exp env variables?)
     *
     * @return object
     */
    public function getOptions(): object;
}
