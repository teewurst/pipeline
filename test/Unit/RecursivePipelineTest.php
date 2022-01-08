<?php
declare(strict_types=1);

namespace teewurst\Pipeline\test\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Pipeline\PayloadInterface;
use teewurst\Pipeline\Pipeline;
use teewurst\Pipeline\RecursivePipeline;

/**
 * Class RecursivePipelineTest
 * @package teewurst\Pipeline\test\Unit
 * @author Martin Ruf <Martin.Ruf@check24.de>
 */
class RecursivePipelineTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @return void
     */
    public function checkIfItReturnsThePayloadOnEmpty(): void
    {
        $payload = $this->prophesize(PayloadInterface::class)->reveal();

        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->handle($payload)->will(
            function ($args) {
                return $args[0];
            }
        );
        $obj = ['any' => 'object'];
        $pipeline->getOptions()->willReturn($obj);

        $recursivePipe = new RecursivePipeline();
        $return = $recursivePipe($payload, $pipeline->reveal());

        self::assertSame($payload, $return);
        self::assertSame($obj, $recursivePipe->getOptions());
    }
}
