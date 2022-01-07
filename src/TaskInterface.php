<?php
declare(strict_types=1);

namespace teewurst\Pipeline;

/**
 * Interface TaskInterface
 *
 * @template T
 *
 * @package teewurst\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
interface TaskInterface
{

    /**
     * Action or single Task to be done in this step
     *
     * @param T                 $payload  Payload containing all Information necessary for this action
     * @param PipelineInterface<T> $pipeline Pipeline currently executed
     *
     * @return T
     */
    public function __invoke($payload, PipelineInterface $pipeline);
}
