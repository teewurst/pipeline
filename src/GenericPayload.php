<?php
declare(strict_types=1);

namespace teewurst\Pipeline;

/**
 * Class GenericPayloadInterface
 *
 * Class offering a simple and fast way to provide a payload. Still I urgently recommend to use the PayloadInterface
 * or at least extend and introduce other interfaces for type hinting!
 *
 * @package teewurst\Pipeline
 * @author  Martin Ruf <Martin.F.Ruf@gmail.com>
 */
class GenericPayload implements PayloadInterface
{
    /** @var array Contains all keys stored in payload*/
    private $keystore = [];

    /**
     * Offers Magic accessor to keystore
     *
     * @param string $name Function Name as setter or gget
     * @param array $arguments Arguments passed into the function
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (preg_match('/(?<method>set|get)(?<key>.+)/', $name, $matches)) {
            return $this->{$matches['method']}(strtolower($matches['key']), ...$arguments);
        }

        throw new \BadMethodCallException('Function ' . $name . ' does not exists!');
    }

    /**
     * Generic getter, which accesses a keystore
     *
     * @param string $name Name of the key in keystore
     *
     * @return mixed|null
     */
    private function get($name)
    {
        return $this->keystore[$name] ?? null;
    }

    /**
     * Generic setter, which accesses a keystore
     *
     * @param string $key Name of the key in keystore
     * @param mixed $value Value to be set within keystore
     *
     * @return void
     */
    private function set($key, $value): void
    {
        $this->keystore[$key] = $value;
    }
}
