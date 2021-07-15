<?php 
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
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
    /**
     * Constructor
     * @param Logo logo
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param Template\Context $context
     * @param Data priceHelper
     * @param TimezoneInterface $timezone
     * @param array data
     */
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

    /**
     * Is payment method mepay
     * @param  string $method
     * @return boolean
     */
    public function isMepay($method)
    {
      return MepayData::isMegaMethod($method);
    }
}