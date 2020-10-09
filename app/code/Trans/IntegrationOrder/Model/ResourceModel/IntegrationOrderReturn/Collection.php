<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderReturn;

use Trans\IntegrationOrder\Model\IntegrationOrderReturn as OrderReturnModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderReturn as OrderReturnResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
	//@codingStandardsIgnoreLine
	protected $_returnId = 'id';

    /**
     * Init resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            OrderReturnModel::class,
            OrderReturnResourceModel::class
        );
        $this->_map['integration_oms_return']['id'] = 'main_table.id';
    }

}
