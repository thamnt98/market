<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Model;

use Trans\Customer\Api\Data\CustomerIntegrationResponseInterface;

/**
 * Class Response
 */
class CustomerIntegrationResponse extends \Magento\Framework\Model\AbstractModel implements CustomerIntegrationResponseInterface
{
    /**
     * @var \Trans\Customer\Api\Data\CustomerIntegrationDataInterfaceFactory
     */
    protected $dataFactory;

    /**
     * CustomerIntegrationResponse constructor.
     * @param \Trans\Customer\Api\Data\CustomerIntegrationDataInterfaceFactory $dataFactory
     */
    public function __construct(
        \Trans\Customer\Api\Data\CustomerIntegrationDataInterfaceFactory $dataFactory
    ) {
        $this->dataFactory = $dataFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(CustomerIntegrationResponseInterface::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(CustomerIntegrationResponseInterface::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getDatas()
    {
        return $this->getData(CustomerIntegrationResponseInterface::DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setDatas($data)
    {
        return $this->setData(CustomerIntegrationResponseInterface::DATA, $data);
    }

}
