<?php
declare(strict_types=1);

namespace teewurst\Pipeline;

/**
 * Class RecursivePipelineHandler
 *
 * Class to build up recursive pipelines, so you are able to create abstracted structure within a pipeline
 *
 * @package teewurst\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class RecursivePipeline extends Pipeline implements TaskInterface
{

    /**
     * Execute internal pipe and pass it to the next handler
     *
     * @param PayloadInterface  $payload  Payload containing all Information necessary for this action
     * @param PipelineInterface $pipeline Pipeline currently executed
     *
     * @return PayloadInterface
     */
    public function __invoke(PayloadInterface $payload, PipelineInterface $pipeline): PayloadInterface
    {
        $this->setOptions($pipeline->getOptions());
        return $pipeline->handle($this->handle($payload));
    }
}
