<?php
declare(strict_types=1);

namespace teewurst\Pipeline;

/**
 * Class RecursivePipelineHandler
 *
 * Class to build up recursive pipelines, so you are able to create abstracted structure within a pipeline
 *
 * @template T of PayloadInterface
 * @implements TaskInterface<T>
 *
 * @package teewurst\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class RecursivePipeline extends Pipeline implements TaskInterface
{

    /**
     * Execute internal pipe and pass it to the next handler
     *
     * @param T  $payload  Payload containing all Information necessary for this action
     * @param PipelineInterface<T> $pipeline Pipeline currently executed
     *
     * @return T
     */
    public function __invoke($payload, PipelineInterface $pipeline)
    {
        $this->setOptions($pipeline->getOptions());
        return $pipeline->handle($this->handle($payload));
    }
}
