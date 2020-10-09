<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_GTM
 *
 * Date: 3/26/20
 * Time: 2:29 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\GTM\Controller\Gtm;

class Refresh extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \SM\GTM\Api\CollectorInterface[]
     */
    protected $collectors;

    /**
     * Refresh constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param array                                 $collectors
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        $collectors = []
    ) {
        parent::__construct($context);
        $this->collectors = $collectors;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        $data = [];
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        foreach ($this->collectors as $key => $collector) {
            if ($collector instanceof \SM\GTM\Api\CollectorInterface) {
                $data[$key] = $collector->collect();
            }
        }

        $resultJson->setData($data);
        $resultJson->setHttpResponseCode(200);

        return $resultJson;
    }
}
