<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 29 2020
 * Time: 11:22 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Plugin\AmastyShopby\Elasticsearch\Model\Adapter;

class AdditionalDataMapper extends \Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\AdditionalDataMapper
{
    public function __construct(array $dataMappers = [])
    {
        unset($dataMappers['am_on_sale']);
        parent::__construct($dataMappers);
    }
}
