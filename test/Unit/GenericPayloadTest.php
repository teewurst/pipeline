<?php
declare(strict_types=1);

namespace teewurst\Pipeline\test\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use teewurst\Pipeline\GenericPayload;

/**
 * Class GenericPayloadTest
 * @package teewurst\Pipeline\test\Unit
 * @author Martin Ruf <Martin.Ruf@check24.de>
 */
class GenericPayloadTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @return void
     */
    public function checkIfUnsetValueReturnsNull(): void
    {
        $payload = new GenericPayload();
        $someFunction = 'getFunction';
        self::assertNull($payload->$someFunction());
    }

    /**
     * @test
     * @return void
     */
    public function checkIfSetWorksTogetherWithGet(): void
    {
        $payload = new GenericPayload();
        $value = 'test';
        $setFunction = 'setFunction';
        $payload->$setFunction($value);
        $getFunction = 'getFunction';
        self::assertSame($value, $payload->$getFunction());
    }

    /**
     * @test
     * @dataProvider invalidFunctionsDataProvider
     * @param string $invalidFunction
     * @return void
     */
    public function checkIfExceptionIsThrownOnInvalidFunction($invalidFunction): void
    {
        $payload = new GenericPayload();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Function ' . $invalidFunction . ' does not exists!');

        $payload->$invalidFunction();
    }

    /**
     * @return array
     */
    public function invalidFunctionsDataProvider(): array
    {
        return [
            [''],
            ['set'],
            ['get'],
            ['anyOther']
        ];
    }
}
