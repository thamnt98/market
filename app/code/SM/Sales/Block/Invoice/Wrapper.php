<?php
/**
 * @category Magento
 * @package SM\Sales\Block\Invoice
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Block\Invoice;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\View\Element\Template;
use Mpdf\Mpdf;
use SM\Sales\Model\InvoiceRepository;

/**
 * Class Wrapper
 * @package SM\Sales\Block\Invoice
 */
class Wrapper extends Template
{
    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    /**
     * Wrapper constructor.
     * @param Template\Context $context
     * @param InvoiceRepository $invoiceRepository
     * @param DirectoryList $directoryList
     * @param SessionFactory $customerSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        InvoiceRepository $invoiceRepository,
        DirectoryList $directoryList,
        SessionFactory $customerSession,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->directoryList = $directoryList;
        $this->sessionFactory = $customerSession;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @param $html
     * @return string
     * @throws \Mpdf\MpdfException
     */
    public function getPDF($html)
    {
        $rootPath  =  $this->directoryList->getRoot();

        $filepath = $rootPath . '/app/code/SM/Sales/view/frontend/web/css/invoice.css';
        $basePath = $rootPath . '/var/tmp/';

        $style = file_get_contents($filepath);

        $mpdf = new Mpdf(["tempDir" => $basePath]);
        $mpdf->shrink_tables_to_fit = 1;

        $mpdf->SetTitle(__('Invoice'));
        $mpdf->WriteHTML($style, 1);

        $mpdf->WriteHTML($html, 2);

        return $mpdf->Output();
    }

    /**
     * @return array|int|\SM\Sales\Api\Data\Invoice\InvoiceInterface
     */
    public function getInvoice()
    {
        $session = $this->sessionFactory->create();
        $customerId = $session->getCustomerId() ?? $session->getCustomerTokenId();

        $parentOrder = $this->getRequest()->getParam("id", 0);
        if ($parentOrder && $customerId) {
            try {
                return $this->invoiceRepository->getDataInvoice($customerId, $parentOrder);
            } catch (\Exception $e) {
                return 0;
            }
        }
        return 0;
    }
}
