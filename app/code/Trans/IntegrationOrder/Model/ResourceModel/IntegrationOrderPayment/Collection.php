<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderPayment;

use Trans\IntegrationOrder\Model\IntegrationOrderPayment as IntegrationOrderPayment;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderPayment as IntegrationOrderPaymentResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
	//@codingStandardsIgnoreLine
	protected $_paymentId = 'oms_id_order_payment';

    /**
     * Init resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            IntegrationOrderPayment::class,
            IntegrationOrderPaymentResourceModel::class
        );
        $this->_map['integration_oms_order_payment']['oms_id_order_payment'] = 'main_table.oms_id_order_payment';
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        return $this;
    }
}
