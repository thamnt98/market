<?php

namespace SM\Sales\Block\Order\Digital\Part;

use SM\Sales\Block\Order\Digital\Detail;

/**
 * Class Content
 * @package SM\Sales\Block\Order\Digital\Part
 */
class Content extends Detail
{
    const TEMPLATE_CONTENT_MOBILE = "SM_Sales::order/detail/digital/part/content/mobile.phtml";
    const TEMPLATE_CONTENT_PLN_BILL = "SM_Sales::order/detail/digital/part/content/pln-bill.phtml";
    const TEMPLATE_CONTENT_PLN_TOKEN = "SM_Sales::order/detail/digital/part/content/pln-token.phtml";

    const TEMPLATE_SUMMARY_MOBILE = "SM_Sales::order/detail/digital/part/summary/mobile.phtml";
    const TEMPLATE_SUMMARY_PLN_BILL = "SM_Sales::order/detail/digital/part/summary/pln-bill.phtml";
    const TEMPLATE_SUMMARY_PLN_TOKEN = "SM_Sales::order/detail/digital/part/summary/pln-token.phtml";

    /**
     * @param string $time
     * @return string
     */
    public function formatExpireDate($time)
    {
        return $this->datetime->date('d F Y, h:i:s A', $time);
    }
}
