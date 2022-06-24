<?php

namespace Survos\BaseBundle\Traits;

use Symfony\Component\PropertyAccess\PropertyAccess;

trait CreateFromTrait
{
    public static function createFrom(\Traversable $data, $constructor_params = [])
    {
        $obj = new static(); // ...$constructor_params); //
        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();

        foreach ($data as $var=>$value) {
            $propertyAccessor->setValue($obj, $var, $value);
        }

        return $obj;

        // https://www.thinktocode.com/2019/09/12/hydrating-query-objects-with-dtos/
        # Construct a reflection method from the constructor and then get all its parameters
        $reflectionMethod = new \ReflectionMethod(static::class, '__construct');
        $reflectionParameters = $reflectionMethod->getParameters();
        $parameters = [];
        # Iterate all the parameters in the constructor and match them with the array keys
        foreach ($reflectionParameters as $reflectionParameter) {
            $parameterName = $reflectionParameter->getName();
            # In case an array key is not found in the constructor, throw an exception
            if (!\array_key_exists($parameterName, $data) && !$reflectionParameter->isOptional()) {
                # In a real project, create your own custom exception class
                throw new \LogicException(
                    'Unable to instantiate \'' . static::class . '\' from an array, argument ' . $parameterName .' is missing.
                     Only the following arguments are available: ' . implode(', ', \array_keys($data)));
            }
            $parameter = $data[$parameterName] ?? $reflectionParameter->getDefaultValue();
            if (\is_array($parameter) && $reflectionParameter->isVariadic()) {
                $parameters = \array_merge($parameters, $parameter);
                continue;
            }
            $parameters[] = $parameter;
        }
        # Create new class with the parameters from the array
        return new static(...$parameters);
    }
}
