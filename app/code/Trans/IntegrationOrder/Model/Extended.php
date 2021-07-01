<?php

namespace Trans\IntegrationOrder\Model;

use Magento\Framework\DataObject;
use Trans\IntegrationOrder\Api\ExtendedInterface;

class Extended extends DataObject implements ExtendedInterface
{
    /**
     * @inheritdoc
     */
    public function ping()
    {
       return 'This is replied messages';
    }
}
