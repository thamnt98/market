<?php
/**
 * @category SM
 * @package SM_Coachmarks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Coachmarks\Ui\Component\Listing\Column;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Store
 *
 * @package SM\Coachmarks\Ui\Component\Listing\Column
 */
class Store extends Column
{
    /**
     * Escaper
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     * System store
     *
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * Store manager
     *
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $storeKey;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SystemStore $systemStore
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     * @param string $storeKey
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SystemStore $systemStore,
        Escaper $escaper,
        array $components = [],
        array $data = [],
        $storeKey = 'store_ids'
    ) {
        $this->systemStore = $systemStore;
        $this->escaper = $escaper;
        $this->storeKey = $storeKey;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     *
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        $origStores = $item[$this->storeKey];
        if (!is_array($origStores)) {
            $origStores = explode(',', $item[$this->storeKey]);
        }
        if (in_array('0', $origStores, true)) {
            return __('All Store Views');
        }
        $data = $this->systemStore->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $content .= "<b>" . $website['label'] . "</b><br/>";
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . "<b>" . $this->escaper->escapeHtml($group['label']) . "</b><br/>";
                foreach ($group['children'] as $store) {
                    $content .= str_repeat('&nbsp;', 6) . $this->escaper->escapeHtml($store['label']) . "<br/>";
                }
            }
        }

        return $content;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if ($this->getStoreManager()->isSingleStoreMode()) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }

    /**
     * Get StoreManager dependency
     *
     * @return StoreManager
     *
     * @deprecated
     */
    private function getStoreManager()
    {
        if ($this->storeManager === null) {
            $this->storeManager = ObjectManager::getInstance()
                ->get(StoreManager::class);
        }

        return $this->storeManager;
    }
}