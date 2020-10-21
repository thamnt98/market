<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: October, 15 2020
 * Time: 3:08 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Controller\Adminhtml\Form;

class Options extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Options constructor.
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Backend\App\Action\Context              $context
     * @param array                                            $options
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\App\Action\Context $context,
        $options = []
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->options = $options;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = [];

        foreach ($this->options as $key => $option) {
            if ($option instanceof \Magento\Framework\Data\OptionSourceInterface) {
                $data[$key] = $option->toOptionArray();
            }
        }

        return $result->setData($data);
    }
}
