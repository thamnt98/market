<?php

namespace SM\Checkout\Plugin\Checkout\Controller;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Checkout\Controller\Index\Index as CheckoutIndex;
use Magento\Framework\UrlInterface;

/**
 * Class Index
 * @package SM\Checkout\Plugin\Checkout\Controller
 */
class Index
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param RedirectFactory $resultRedirectFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        RedirectFactory $resultRedirectFactory,
        UrlInterface $urlBuilder
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param CheckoutIndex $subject
     * @param callable $proceed
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(
        CheckoutIndex $subject,
        $proceed
    ) {
        $url = trim($this->urlBuilder->getUrl('transcheckout'), '/');
        return $this->resultRedirectFactory->create()->setUrl($url);
    }
}
