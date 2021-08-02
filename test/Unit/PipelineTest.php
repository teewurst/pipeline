<?php
declare(strict_types=1);

namespace teewurst\Pipeline\test\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Pipeline\PayloadInterface;
use teewurst\Pipeline\Pipeline;
use teewurst\Pipeline\PipelineInterface;
use teewurst\Pipeline\TaskInterface;

/**
 * Class PipelineTest
 * @package teewurst\Pipeline\test\Unit
 * @author Martin Ruf <Martin.Ruf@check24.de>
 */
class PipelineTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @return void
     */
    public function checkIfNextReducesTheKeystore(): void
    {
        $testArray = [
            $this->prophesize(TaskInterface::class)->reveal(),
            $this->prophesize(TaskInterface::class)->reveal(),
            $this->prophesize(TaskInterface::class)->reveal()
        ];

        $pipeline = new Pipeline($testArray);
        while ($testArray) {
            self::assertSame(array_shift($testArray), $pipeline->next());
        }
    }

    /**
     * @test
     * @return void
     */
    public function checkIfPipelineReturnsNullOnEmpty(): void
    {
        $pipeline = new Pipeline();
        self::assertNull($pipeline->next());
    }

    /**
     * @test
     * @return void
     */
    public function checkIfAddAddsATaskToThePipeline(): void
    {
        $pipeline = new Pipeline();

        $task = $this->prophesize(TaskInterface::class)->reveal();
        $pipeline->add($task);

        self::assertSame($task, $pipeline->next());
    }

    /**
     * @test
     * @return void
     */
    public function checkIfHandleReturnsThePayloadOnEmptyTasks(): void
    {
        $pipeline = new Pipeline();

        $payload = $this->prophesize(PayloadInterface::class)->reveal();
        $return = $pipeline->handle($payload);

        self::assertSame($payload, $return);
    }

    /**
     * @test
     * @return void
     */
    public function checkIfHandleReturnsTheSamePayload(): void
    {
        $task = $this->getMockBuilder(TaskInterface::class)
            ->setMethods([
                '__invoke'
            ])
            ->getMock();

        $payload = $this->prophesize(PayloadInterface::class)->reveal();

        $pipeline = new Pipeline([$task]);

        $task->expects($this->once())
            ->method('__invoke')
            ->with($payload, $pipeline)
            ->willReturn($payload);
        $return = $pipeline->handle($payload);

        self::assertSame($payload, $return);
    }

    /**
     * @test
     * @return void
     */
    public function checkIfOtherExceptionsArePassedThrough(): void
    {

        $payload = $this->prophesize(PayloadInterface::class);

        $pipeline = new Pipeline();

        /** @phpstan-ignore $task */
        $task = $this->getMockBuilder(TaskInterface::class)
                     ->setMethods([
                         '__invoke'
                     ])
                     ->getMock();

        $task->expects($this->once())
             ->method('__invoke')
             ->with($payload->reveal(), $pipeline)
             ->willThrowException(new \Exception('Any exception'));

        $pipeline->add($task);

        $this->expectExceptionMessage('Any exception');
        $this->expectException(\Exception::class);

        $pipeline->handle($payload->reveal());
    }

    /** @test */
    public function checkIfPipelineCanBeExecutedMultipleTimes(): void
    {
        $payloadMock = $this->prophesize(PayloadInterface::class);

        $taskBuilder = function () use ($payloadMock): TaskInterface {
            $taskMock = $this->prophesize(TaskInterface::class);

            $taskMock->__invoke(
                    $payloadMock->reveal(),
                    Argument::type(PipelineInterface::class)
                )->shouldBeCalledTimes(2)
                ->will(function ($args) {
                    return $args[1]->handle($args[0]);
                }); // assertion

            return $taskMock->reveal();
        };

        $tasks = [
            $taskBuilder(),
            $taskBuilder(),
            $taskBuilder(),
        ];

        $pipeline = new Pipeline($tasks);
        $pipeline->handle($payloadMock->reveal());
        $pipeline->handle($payloadMock->reveal());
    }
}
