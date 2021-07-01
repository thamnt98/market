<?php
/**
 * SM\TodayDeal\Plugin
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TodayDeal\Plugin;

use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\UrlInterface;

/**
 * Class PrependBreadcrumb
 * @package SM\TodayDeal\Plugin
 */
class PrependBreadcrumb
{
    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * PrependBreadcrumb constructor.
     * @param UrlInterface $url
     * @param RedirectInterface $redirect
     */
    public function __construct(UrlInterface $url, RedirectInterface $redirect)
    {
        $this->url = $url;
        $this->redirect = $redirect;
    }

    /**
     * @param \Magento\Catalog\Helper\Data $subject
     * @param $result
     * @return array[]
     */
    public function afterGetBreadcrumbPath(\Magento\Catalog\Helper\Data $subject, $result)
    {
        $currentUrl = $this->url->getCurrentUrl();
        $referrerUrl = $this->redirect->getRefererUrl();
        if ($this->isTodayDeal($currentUrl) || $this->isTodayDeal($referrerUrl)) {
            $crumb = [
                "todaydeal" => [
                    'label' => __("Curated For You"),
                    'link' => $this->url->getUrl("curatedforyou")
                ]
            ];
            return $crumb + $result;
        }

        return $result;
    }

    /**
     * @param $currentUrl
     * @return bool
     */
    public function isTodayDeal($currentUrl)
    {
        $split = explode("/", $currentUrl);
        if (isset($split[3]) && ($split[3]) == "todaydeal") {
            return true;
        }
        return false;
    }
}
