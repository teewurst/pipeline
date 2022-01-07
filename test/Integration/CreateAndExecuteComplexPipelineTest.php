<?php

declare(strict_types=1);

namespace teewurst\Pipeline\test\Integration;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Pipeline\GenericPayload;
use teewurst\Pipeline\PayloadInterface;
use teewurst\Pipeline\Pipeline;
use teewurst\Pipeline\PipelineInterface;
use teewurst\Pipeline\PipelineService;
use teewurst\Pipeline\TaskInterface;

/**
 * Class CreateAndExecuteComplexPipelineTest
 * @package teewurst\Pipeline\test\Integration
 * @author Martin Ruf <Martin.Ruf@check24.de>
 */
class CreateAndExecuteComplexPipelineTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @return void
     */
    public function checkIfPipelineIsExecutedCorrectly(): void
    {
        $payload = new GenericPayload();
        $payload->setBefore(0);
        $payload->setAfter(0);

        $pipelineService = new PipelineService();

        $options = ['any' => 'test'];
        $pipeline = $pipelineService->create(
            [
                $this->createTestTaskMock($options),
                $this->createTestTaskMock($options),
                [
                    $this->createTestTaskMock($options),
                    [
                        [
                            $this->createTestTaskMock($options),
                            $this->createTestTaskMock($options),
                        ],
                        $this->createTestTaskMock($options),
                        $this->createTestTaskMock($options)
                    ],
                    $this->createTestTaskMock($options)
                ]
            ],
            Pipeline::class,
            $options
        );

        $resultPayload = $pipeline->handle($payload);

        self::assertSame(8, $resultPayload->getBefore());
        self::assertSame(8, $resultPayload->getAfter());
    }

    /**
     * @param $options
     * @return object|TaskInterface
     */
    public function createTestTaskMock($options) {
        $task = $this->prophesize(TaskInterface::class)
            ->willImplement(TaskInterface::class);
        $self = $this;
        $task->__invoke(
            Argument::type(PayloadInterface::class),
            Argument::type(PipelineInterface::class)
        )->shouldBeCalled()->will(
            function ($args) use ($options, $self) {
                /** @var PipelineInterface<GenericPayload> $pipeline */
                $pipeline = $args[1];
                /** @var GenericPayload $payload */
                $payload = $args[0];

                // check access to options
                $self::assertSame($options, $pipeline->getOptions());
                // validate payload handling before and after
                $payload->setBefore($payload->getBefore() + 1);
                //execute next task
                $payload = $pipeline->handle($payload);
                $payload->setAfter($payload->getAfter() + 1);
                return $payload;
            }
        );
        return $task->reveal();
    }
}
