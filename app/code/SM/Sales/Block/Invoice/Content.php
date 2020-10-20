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

use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Header\Logo;
use SM\Sales\Api\Data\Invoice\InvoiceInterface;
use SM\Sales\Api\InvoiceRepositoryInterface;

/**
 * Class Content
 * @package SM\Sales\Block\Invoice
 */
class Content extends Template
{
    /**
     * @var Logo
     */
    protected $logo;
    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;
    /**
     * @var InvoiceInterface
     */
    protected $invoice;
    /**
     * @var Data
     */
    protected $priceHelper;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Content constructor.
     * @param Logo $logo
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param Template\Context $context
     * @param Data $priceHelper
     * @param TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
        Logo $logo,
        InvoiceRepositoryInterface $invoiceRepository,
        Template\Context $context,
        Data $priceHelper,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        $this->timezone = $timezone;
        $this->priceHelper = $priceHelper;
        parent::__construct($context, $data);
        $this->invoiceRepository = $invoiceRepository;
        $this->logo = $logo;
    }

    /**
     * @param float $value
     * @return float|string
     */
    public function currencyFormat($value)
    {
        return $this->priceHelper->currency($value, true, false);
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo->getLogoSrc();
    }

    /**
     * @param $value
     * @return $this
     */
    public function setInvoice($value)
    {
        $this->invoice = $value;
        return $this;
    }

    /**
     * @return InvoiceInterface
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    public function prepareStreet($street)
    {
        $streetPart = explode(", ", $street);
        $firstLine = $secondLine = "";
        if (count($streetPart)) {
            $secondLine = array_pop($streetPart);
            $firstLine = implode(", ", $streetPart);
        }
        return [
            "street" => $firstLine,
            "last" => $secondLine
        ];
    }

    public function pickUpTimeDateFormat($date)
    {
        return $this->timezone->date($date)->format('d F Y');
    }
}
