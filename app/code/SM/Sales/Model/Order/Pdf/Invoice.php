<?php

namespace SM\Sales\Model\Order\Pdf;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Sales\Model\Order\Pdf\ItemsFactory;
use Magento\Sales\Model\Order\Pdf\Total\Factory;
use Magento\Store\Model\StoreManagerInterface;
use Mpdf\Mpdf;
use SM\Sales\Api\InvoiceRepositoryInterface;

/**
 * Class Invoice
 * @package SM\Sales\Model\Order\Pdf
 */
class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{
    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;
    /**
     * @var BlockFactory
     */
    protected $blockFactory;
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * Invoice constructor.
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param Data $paymentData
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @param Config $pdfConfig
     * @param Factory $pdfTotalFactory
     * @param ItemsFactory $pdfItemsFactory
     * @param TimezoneInterface $localeDate
     * @param StateInterface $inlineTranslation
     * @param Renderer $addressRenderer
     * @param StoreManagerInterface $storeManager
     * @param ResolverInterface $localeResolver
     * @param BlockFactory $blockFactory
     * @param DirectoryList $directoryList
     * @param array $data
     */
    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        Data $paymentData,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        Config $pdfConfig,
        Factory $pdfTotalFactory,
        ItemsFactory $pdfItemsFactory,
        TimezoneInterface $localeDate,
        StateInterface $inlineTranslation,
        Renderer $addressRenderer,
        StoreManagerInterface $storeManager,
        ResolverInterface $localeResolver,
        BlockFactory $blockFactory,
        DirectoryList $directoryList,
        array $data = []
    ) {
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $storeManager,
            $localeResolver,
            $data
        );
        $this->blockFactory = $blockFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->directoryList = $directoryList;
    }

    public function getPdf($invoices = [])
    {
        $invoiceList = [];
        foreach ($invoices as $invoice) {
            $invoiceList[] = $invoice;
        }

        $invoice = $this->invoiceRepository->getById($invoiceList[0]->getOrder()->getData('entity_id'));

        $html = $this->getHTML($invoice);

        $rootPath  =  $this->directoryList->getRoot();

        $filepath = $rootPath . '/app/code/SM/Sales/view/frontend/web/css/invoice.css';
        $basePath = $rootPath . '/var/tmp/';
        $style = file_get_contents($filepath);

        $mpdf = new Mpdf(["tempDir" => $basePath]);
        $mpdf->shrink_tables_to_fit = 1;

        $mpdf->SetTitle('Invoice');
        $mpdf->WriteHTML($style, 1);

        $mpdf->WriteHTML($html, 2);

        return $mpdf->Output();
    }

    /**
     * @param \SM\Sales\Api\Data\Invoice\InvoiceInterface $invoice
     * @return string
     */
    protected function getHTML($invoice)
    {
        /** @var \SM\Sales\Block\Invoice\Content $contentBlock */
        $contentBlock = $this->blockFactory->createBlock("SM\Sales\Block\Invoice\Content");
        $contentBlock->setTemplate("SM_Sales::invoice/content.phtml");
        $contentBlock->setInvoice($invoice);
        return $contentBlock->toHtml();
    }
}
