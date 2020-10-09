<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Model;

use Trans\LocationCoverage\Api\Data\CityInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class CityFactory
 * @package Trans\LocationCoverage\Model
 */
class CityFactory implements CityFactoryInterface
{
    /**
     * Object Manager instance
     * @var ObjectManagerInterface
     */
    private $objectManager = null;

    /**
     * Instance name to create
     * @var string
     */
    private $instanceName = null;

    /**
     * CityFactory constructor.
     * @param ObjectManagerInterface $objectManager
     * @param $instanceName
     */
    public function __construct(ObjectManagerInterface $objectManager, $instanceName = CityInterface::class)
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}