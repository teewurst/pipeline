<?php

declare(strict_types=1);

namespace teewurst\Pipeline\test\Integration;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Pipeline\GenericPayload;
use teewurst\Pipeline\PayloadInterface;
use teewurst\Pipeline\PipelineInterface;
use teewurst\Pipeline\PipelineService;
use teewurst\Pipeline\TaskInterface;

/**
 * Class CreateAndInterruptByExceptionTest
 * @package teewurst\Pipeline\test\Integration
 * @author Martin Ruf <Martin.Ruf@check24.de>
 */
class CreateAndInterruptByExceptionTest extends TestCase
{
    use ProphecyTrait;

    private const CUSTOM_EXCEPTION = 'CustomException';

    /**
     * @test
     * @return void
     */
    public function checkExecutionIsCatchableInTask(): void
    {
        $payload = new GenericPayload();

        $payloadService = new PipelineService();
        $pipeline = $payloadService->create([
            $this->getSimpleTask(),
            $this->getExceptionTask(),
            $this->getDoNotExecuteTask()
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(self::CUSTOM_EXCEPTION);
        $pipeline->handle($payload);
    }

    /**
     * @return TaskInterface
     */
    private function getSimpleTask()
    {
        $task = $this->prophesize(TaskInterface::class);
        $task->__invoke(
            Argument::type(PayloadInterface::class),
            Argument::type(PipelineInterface::class)
        )->shouldBeCalled()->will(
            function ($args) {
                /** @var PipelineInterface $pipeline */
                $pipeline = $args[1];
                /** @var PayloadInterface $payload */
                $payload = $args[0];
                $payload = $pipeline->handle($payload);
                return $payload;
            }
        );
        return $task->reveal();
    }

    /**
     * @return object|TaskInterface
     */
    private function getExceptionTask()
    {
        $task = $this->prophesize(TaskInterface::class);
        $task->__invoke(
            Argument::type(PayloadInterface::class),
            Argument::type(PipelineInterface::class)
        )->shouldBeCalled()->willThrow(new \RuntimeException(self::CUSTOM_EXCEPTION));
        return $task->reveal();
    }

    /**
     * @return object|TaskInterface
     */
    private function getDoNotExecuteTask()
    {
        $task = $this->prophesize(TaskInterface::class);
        $task->__invoke(
            Argument::type(PayloadInterface::class),
            Argument::type(PipelineInterface::class)
        )->shouldNotBeCalled();
        return $task->reveal();
    }
}
