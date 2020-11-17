<?php 
namespace Trans\MepayTransmart\Block\Invoice;

use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Header\Logo;
use SM\Sales\Api\Data\Invoice\InvoiceInterface;
use SM\Sales\Api\InvoiceRepositoryInterface;
use SM\Sales\Block\Invoice\Content;
use Trans\Mepay\Helper\Data as MepayData;

class TransmartContent extends Content
{
    public function __construct(
        Logo $logo,
        InvoiceRepositoryInterface $invoiceRepository,
        Template\Context $context,
        Data $priceHelper,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        parent::__construct(
          $logo,
          $invoiceRepository,
          $context,
          $priceHelper,
          $timezone,
          $data
        );
    }

    public function isMepay($method)
    {
      return MepayData::isMegaMethod($method);
    }
}