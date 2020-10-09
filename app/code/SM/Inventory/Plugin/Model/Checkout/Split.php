<?php
/**
 * Class Split
 * @package SM\Inventory\Plugin\Model\Checkout
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Inventory\Plugin\Model\Checkout;

class Split
{
    /**
     * @var \SM\Inventory\Helper\ConfigurableStock
     */
    private $configurableStock;

    /**
     * Split constructor.
     * @param \SM\Inventory\Helper\ConfigurableStock $configurableStock
     */
    public function __construct(
        \SM\Inventory\Helper\ConfigurableStock $configurableStock
    ) {
        $this->configurableStock = $configurableStock;
    }

    /**
     * @param \SM\Checkout\Model\Split $subject
     * @param array $data
     * @return array[]
     */
    public function beforeGetOarResponse(
        \SM\Checkout\Model\Split $subject,
        array $data
    ): array {

        foreach ($data as &$address) {
            if (isset($address['items'])) {
                foreach ($address['items'] as &$item) {
                    $item['sku_basic'] = '';
                    if ($this->configurableStock->checkIsConfigurableBaseSku($item['sku'])
                        || $this->configurableStock->checkIsConfigurableStockSku($item['sku'])
                    ) {
                        $item['sku_basic'] = $this->configurableStock->getBaseSku($item['sku']);
                    }
                }
            }
        }
        return array($data);
    }
}
