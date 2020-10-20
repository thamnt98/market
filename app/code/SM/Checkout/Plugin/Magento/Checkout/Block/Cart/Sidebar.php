<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 4/7/20
 * Time: 4:21 PM
 */

namespace SM\Checkout\Plugin\Magento\Checkout\Block\Cart;

class Sidebar
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Sidebar constructor.
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param $result
     * @return bool
     */
    public function afterGetIsNeedToDisplaySideBar(\Magento\Checkout\Block\Cart\Sidebar $subject, $result)
    {
        $currentUrl = $this->request->getFullActionName();
        $isCartPage = false;
        if ($currentUrl == 'checkout_cart_index') {
            $isCartPage = true;
        }
        return $result && !$isCartPage;
    }
}