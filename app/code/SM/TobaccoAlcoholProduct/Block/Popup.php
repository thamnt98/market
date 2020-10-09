<?php
/**
 * SM\TobaccoAlcoholProduct\Block
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TobaccoAlcoholProduct\Block;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Layer;
use Magento\Framework\View\Element\Template;
use SM\TobaccoAlcoholProduct\Helper\PopupAlcohol;

/**
 * Class Popup
 * @package SM\TobaccoAlcoholProduct\Block
 */
class Popup extends Template
{
    protected $_template = "SM_TobaccoAlcoholProduct::popup.phtml";

    /**
     * @var PopupAlcohol
     */
    protected $popupHelper;

    /**
     * Popup constructor.
     * @param Template\Context $context
     * @param PopupAlcohol $popupHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PopupAlcohol $popupHelper,
        array $data = []
    ) {
        $this->popupHelper = $popupHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool|null
     */
    public function isPlp()
    {
        return ($this->getData("is_plp") ?? false) && !$this->isBrandPage();
    }

    /**
     * @return bool|null
     */
    public function isPdp()
    {
        return $this->getData("is_pdp") ?? false;
    }

    /**
     * @return bool|null
     */
    public function isSearch()
    {
        return $this->getData("is_search") ?? false;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsPlp(bool $value)
    {
        return $this->setData("is_plp", $value);
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsSearch(bool $value)
    {
        return $this->setData("is_search", $value);
    }

    /**
     * @return bool
     */
    public function isShow()
    {
        $customer = $this->popupHelper->getCurrentCustomer($this->isSearch());
        if (!$this->popupHelper->isCustomerInformed($customer)) {
            /**
             * For both case PLP and Landing Page
             */
            if ($this->isPlp() && $this->popupHelper->isCategoryAlcohol($this->popupHelper->getCurrentCategory())) {
                return true;
            }

            /**
             * For PDP
             */
            if ($this->isPdp() && $this->popupHelper->isProductAlcohol($this->popupHelper->getCurrentProduct())) {
                return true;
            }

            /**
             * Alcohol product was checked in search results, so don't need to re-check here
             */
            if ($this->isSearch()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check current page is brand page
     *
     * @return bool
     */
    public function isBrandPage()
    {
        return $this->_request->getModuleName() == "ambrand";
    }
}
