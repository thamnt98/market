<?php


namespace SM\Sales\Block\Order\Email;


use Magento\Catalog\Helper\Image;
use Magento\Framework\View\Element\Template;

class Items extends \Magento\Sales\Block\Order\Email\Items
{
    /**
     * @var Image
     */
    protected $imageHelper;

    public function __construct(
        Template\Context $context,
        Image $imageHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->imageHelper = $imageHelper;
    }

    /**
     * Get Thumbnail Product of Order
     * @param $item
     * @param $width
     * @param $height
     * @return string
     */
    public function getImage($item, $width = null, $height = null)
    {
        return $this->imageHelper->init($item, 'cart_page_product_thumbnail')
            ->setImageFile($item->getImage())
            ->resize($width, $height)
            ->getUrl();
    }

    public function getInstallationItems($order)
    {
        $items = [];
        if ($order) {
            foreach ($order->getAllItems() as  $item) {
                $buyRequest = $item->getProductOptionByCode('info_buyRequest');
                if (isset($buyRequest['installation_service'])) {
                    if ($buyRequest['installation_service']['is_installation'] == 1) {
                        $items[] = $item;
                    }
                }
            }
        }
        return $items;
    }
}
