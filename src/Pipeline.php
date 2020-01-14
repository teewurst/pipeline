<?php
declare(strict_types=1);

namespace teewurst\Pipeline;

use teewurst\Pipeline\Exceptions\BadMethodCallException;

/**
 * Class Pipeline
 *
 * Classic pipeline
 *
 * @package teewurst\Pipeline
 * @author  Martin Ruf <Martin.Ruf@check24.de>
 */
class Pipeline implements PipelineInterface
{
    use DefaultPipelineTrait;

    /**
     * Start execution of all tasks within the pipeline
     *
     * @param PayloadInterface $payload Payload to be passed through all tasks
     *
     * @return PayloadInterface
     */
    public function handle(PayloadInterface $payload): PayloadInterface
    {
        $task = $this->next();

        if ($task === null) {
            return $payload;
        }

        return $task($payload, $this);
    }
}
