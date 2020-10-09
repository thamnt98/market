<?php

namespace SM\GTM\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Reflection\MethodsMap;
use Magento\Framework\Reflection\TypeProcessor;

/**
 * Class ObjectProcessor
 * @package Tops\DataIntegration\Model
 */
class ObjectProcessor
{
    const EXPLODE_DELIMITER = '.';
    const GET_FUNCTION_PREFIX = 'get';
    const SET_FUNCTION_PREFIX = 'set';

    /**
     * @var TypeProcessor
     */
    private $typeProcessor;

    /**
     * @var MethodsMap
     */
    private $methodsMap;

    /**
     * ObjectProcessor constructor.
     * @param MethodsMap $methodsMap
     * @param TypeProcessor|null $typeProcessor
     */
    public function __construct(
        MethodsMap $methodsMap,
        TypeProcessor $typeProcessor = null
    ) {
        $this->methodsMap = $methodsMap;
        $this->typeProcessor = $typeProcessor ?: ObjectManager::getInstance()->get(TypeProcessor::class);
    }

    /**
     * @param object $object
     * @param string $path
     * @return mixed
     */
    public function getValueByPath($object, $path)
    {
        return $this->extractObjectData($object, $path);
    }

    /**
     * @param object $object
     * @param string $path
     * @param mixed $value
     * @return object
     */
    public function apply($object, $path, $value)
    {
        $this->setRecursiveObjectData($object, $path, $value);
        return $object;
    }

    /**
     * @param object $object
     * @param string $path
     * @return mixed
     */
    private function extractObjectData($object, string $path)
    {
        $explodedPath = explode(self::EXPLODE_DELIMITER, $path);
        $identifier = array_shift($explodedPath);
        $getterFunction = self::GET_FUNCTION_PREFIX . $this->convertSnakeCaseToCamelCase($identifier);

        if (!method_exists($object, $getterFunction) && !method_exists($object, 'getData')
        ) {
            return 'null';
        }

        if ($object->{$getterFunction}() === null) {
            return 'null';
        }

        if (count($explodedPath) === 0) {
            return $object->{$getterFunction}();
        }

        return $this->extractObjectData($object->{$getterFunction}(), implode(self::EXPLODE_DELIMITER, $explodedPath));
    }

    /**
     * @param object $object
     * @param string $path
     * @param mixed $value
     * @return mixed
     */
    private function setRecursiveObjectData($object, string $path, $value)
    {
        $explodedPath = explode(self::EXPLODE_DELIMITER, $path);
        $identifier = array_shift($explodedPath);
        $setterFunction = self::SET_FUNCTION_PREFIX . $this->convertSnakeCaseToCamelCase($identifier);
        $getterFunction = self::GET_FUNCTION_PREFIX . $this->convertSnakeCaseToCamelCase($identifier);

        if (count($explodedPath) === 0) {
            return $object->setData($identifier, $value);
        }

        if ($object->{$getterFunction}() === null) {
            //Get Definition from class and init object if needed.
            $className = get_class($object);
            $returnTypes = $this->methodsMap->getMethodReturnType($className, $getterFunction);

            $newObject = ObjectManager::getInstance()->get($returnTypes);

            $object->{$setterFunction}($newObject);
        }

        return $this->setRecursiveObjectData(
            $object->{$getterFunction}(),
            implode(self::EXPLODE_DELIMITER, $explodedPath),
            $value
        );
    }

    /**
     * @param string $input
     * @param string $separator
     * @return string
     */
    private function convertSnakeCaseToCamelCase($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }
}
