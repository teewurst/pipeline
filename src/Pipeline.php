<?php
declare(strict_types=1);

namespace teewurst\Pipeline;

/**
 * Class Pipeline
 *
 * Classic pipeline
 *
 * @template T
 * @implements PipelineInterface<T>
 *
 * @package teewurst\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class Pipeline implements PipelineInterface
{
    /** @use PipelineTrait<T> */
    use PipelineTrait;
    /**
     * DefaultPipelineTrait constructor.
     *
     * @param TaskInterface<T>[] $tasks Array of tasks
     */
    public function __construct(array $tasks = [])
    {
        $this->tasks = $tasks;
    }
}
