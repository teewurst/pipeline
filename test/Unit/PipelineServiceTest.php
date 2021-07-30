<?php

declare(strict_types=1);

namespace teewurst\Pipeline\test\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use teewurst\Pipeline\Pipeline;
use teewurst\Pipeline\PipelineService;
use teewurst\Pipeline\TaskInterface;

/**
 * Class PipelineServiceTest
 * @package teewurst\Pipeline\test\Unit
 * @author Martin Ruf <Martin.Ruf@check24.de>
 */
class PipelineServiceTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @return void
     */
    public function checkIfOptionsArePassedIntoPipeline(): void
    {
        $options = (object)['any' => 'options'];

        $pipelineService = new PipelineService();
        $pipeline = $pipelineService->create([], $options);

        self::assertSame($options, $pipeline->getOptions());
    }

    /**
     * @test
     * @return void
     */
    public function checkIfPsr11ContainerIsUsedCorrectly(): void
    {
        $task = $this->prophesize(TaskInterface::class);

        $container = $this->prophesize(ContainerInterface::class);
        $serviceHash = 'any';
        $container->get($serviceHash)->shouldBeCalled()->willReturn($task->reveal());

        $pipelineService = new PipelineService();

        self::assertInstanceOf(
            Pipeline::class,
            $pipelineService->createPsr11($container->reveal(), [$serviceHash])
        );
    }

    /**
     * @test
     * @return void
     */
    public function checkIfExceptionIsThrownIfInvalidTask(): void
    {
        $pipelineService = new PipelineService();

        $this->expectException(\InvalidArgumentException::class);

        $pipelineService->create(['WrongType']);
    }
}
