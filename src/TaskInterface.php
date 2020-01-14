<?php
declare(strict_types=1);

namespace teewurst\Pipeline;

/**
 * Interface TaskInterface
 *
 * @package teewurst\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
interface TaskInterface
{

    /**
     * Action or single Task to be done in this step
     *
     * @param PayloadInterface  $payload  Payload containing all Information necessary for this action
     * @param PipelineInterface $pipeline Pipeline currently executed
     *
     * @return PayloadInterface
     */
    public function __invoke(PayloadInterface $payload, PipelineInterface $pipeline): PayloadInterface;
}
