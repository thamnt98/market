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
     * Wrapper constructor.
     * @param Template\Context $context
     * @param InvoiceRepository $invoiceRepository
     * @param DirectoryList $directoryList
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        InvoiceRepository $invoiceRepository,
        DirectoryList $directoryList,
        array $data = []
    ) {
        $this->invoiceRepository = $invoiceRepository;
        parent::__construct($context, $data);
        $this->directoryList = $directoryList;
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
        $parentOrder = $this->getRequest()->getParam("id", 0);
        if ($parentOrder) {
            try {
                return $this->invoiceRepository->getById($parentOrder);
            } catch (\Exception $e) {
                return 0;
            }
        }
        return 0;
    }
}
