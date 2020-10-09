<?php

namespace SM\GTM\Model\Mapper;

use Magento\Framework\DataObject;
use SM\GTM\Api\EncryptorInterface;
use SM\GTM\Api\MapperInterface;
use SM\GTM\Model\ObjectProcessor;

/**
 * Class Mapper
 * @package SM\GTM\Model\Mapper
 */
class Mapper implements MapperInterface
{
    const DEFAULT_VALUE = '';

    /**
     * @var array
     */
    private $mappingFields;

    /**
     * @var ObjectProcessor
     */
    private $objectProcessor;

    /**
     * @var array
     */
    private $secureFields;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * Mapper constructor.
     * @param ObjectProcessor $objectProcessor
     * @param EncryptorInterface $encryptor
     * @param array $mappingFields
     * @param array $secureFields
     */
    public function __construct(
        ObjectProcessor $objectProcessor,
        EncryptorInterface $encryptor,
        $mappingFields = [],
        $secureFields = []
    ) {
        $this->objectProcessor = $objectProcessor;
        $this->mappingFields = $mappingFields;
        $this->secureFields = $secureFields;
        $this->encryptor = $encryptor;
    }

    /**
     * @inheritDoc
     */
    public function map($object)
    {
        $destinationObject = new DataObject();

        if (!is_object($object)) {
            return $destinationObject;
        }

        foreach ($this->mappingFields as $destinationField => $sourceField) {
            $value = $this->objectProcessor->getValueByPath($object, $sourceField);
            // Check for existing of $destinationField in $object
            if (in_array($destinationField, $this->secureFields)) {
                //Encypted with SHA256
                $value = $this->encryptor->encrypt($value ?: '');
            }

            if (is_null($value)) {
                $value = self::DEFAULT_VALUE;
            }

            //Here map the value from $sourceField to $destination
            $this->objectProcessor->apply($destinationObject, $destinationField, $value);
        }

        return $destinationObject;
    }
}
