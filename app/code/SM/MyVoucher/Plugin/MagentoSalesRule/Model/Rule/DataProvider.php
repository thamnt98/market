<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_MyVoucher
 *
 * Date: July, 15 2020
 * Time: 10:59 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\MyVoucher\Plugin\MagentoSalesRule\Model\Rule;

class DataProvider
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DataProvider constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\DataProvider $subject
     * @param array                                      $result
     *
     * @return array
     */
    public function afterGetData(\Magento\SalesRule\Model\Rule\DataProvider $subject, $result)
    {
        if (is_array($result)) {
            foreach ($result as &$item) {
                if (empty($item['voucher_image'])) {
                    continue;
                }

                $name = $item['voucher_image'];
                $item['voucher_image'] = [];
                $item['voucher_image'][0] = [
                    'name' => $name,
                    'url'  => $this->getImageUrl($name),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getImageUrl($name)
    {
        try {
            return $this->storeManager->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'sm/tmp/icon/' . $name;
        } catch (\Exception $e) {
            return '';
        }
    }
}
