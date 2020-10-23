<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: July, 10 2020
 * Time: 2:04 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;

class Validate extends Action implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SM_Notification::manage';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $formFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formFactory = $formFactory;
    }

    /**
     * AJAX customer address validation action
     *
     * @return Json
     */
    public function execute(): Json
    {
        $error = [];
        $response = new \Magento\Framework\DataObject();
        $response->setData('error', false);

        $data = $this->getRequest()->getPostValue();
        if (empty($data['start_date']) ||
            empty($data['end_date']) ||
            empty($data['event']) ||
            empty($data['admin_type']) ||
            empty($data['title'])
        ) {
            $error[] = __('Field(s) `*` is required!');
        }

        if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
            $error[] = __('`End Date` must be after `Start Date`!');
        }

        if ($data['admin_type'] == \SM\Notification\Model\Source\CustomerType::TYPE_CUSTOMER &&
            empty($data['customer_ids'])
        ) {
            $error[] = __('`Customer (s)` is required!');
        } elseif ($data['admin_type'] == \SM\Notification\Model\Source\CustomerType::TYPE_CUSTOMER_SEGMENT &&
            empty($data['segment_ids'])
        ) {
            $error[] = __('`Segment (s)` is required!');
        } elseif (strlen($data['content']) > 500) {
            $error[] = __('`Content` must be less than or equal to 500 characters.');
        } elseif (strlen($data['push_content']) > 500) {
            $error[] = __('`Push Device Content` must be less than or equal to 500 characters.');
        }

        if (count($error)) {
            $response->setData('error', true);
            $response->setData('messages', $error);
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);

        return $resultJson;
    }
}
