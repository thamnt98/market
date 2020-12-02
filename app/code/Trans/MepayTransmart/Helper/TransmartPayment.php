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
namespace Trans\MepayTransmart\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Helper\Data as PricingData;
use Magento\Quote\Model\Quote\Address;
use Trans\Sprint\Helper\Config;
use SM\Checkout\Helper\Payment;

class TransmartPayment extends Payment 
{
    /**
     * @var \SM\Checkout\Api\Data\Checkout\PaymentMethods\HowToPayInterfaceFactory
     */
    private $howToPayFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        Session $sessionCheckout,
        Config $configHelper,
        PricingData $pricingData,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Checkout\Api\Data\Checkout\PaymentMethods\HowToPayInterfaceFactory $howToPayFactory
    ) {
        $this->howToPayFactory = $howToPayFactory;
        parent::__construct(
          $scopeConfig,
          $blockRepository,
          $sessionCheckout,
          $configHelper,
          $pricingData,
          $json,
          $storeManager,
          $howToPayFactory
        );
    }

    public function getBlockHowToPay($paymentMethod = '', $toArray = false)
    {
        $blockId = 'how-to-pay';
        $howToPay = [];
        if ($paymentMethod) {
            $blockIdTmp = $blockId;
            for ($i = 1; $i < 10; $i++) {
                $blockId = $blockIdTmp . '-' . $paymentMethod . '-' . $i;
                try {
                    $block = $this->blockRepository->getById($blockId);
                    $content = $block->getContent();
                    if (empty($content)) {
                        continue;
                    }

                    if ($toArray) {
                        $content = preg_split('/\r\n|\r|\n/', strip_tags($content));
                    }
                    $howToPayItem = $this->howToPayFactory->create();
                    $howToPayItem->setBlockTitle($block->getTitle());
                    $howToPayItem->setBlockContent($content);
                    $howToPay[] = $howToPayItem;
                } catch (LocalizedException $e) {
                    continue;
                }
            }

        } else {


                $blockIdTmp = $blockId;
                $blockId = $blockIdTmp;
                try {
                    $block = $this->blockRepository->getById($blockId);
                    $content = $block->getContent();

                    if ($toArray) {
                        $content = preg_split('/\r\n|\r|\n/', strip_tags($content));
                    }
                    $howToPayItem = $this->howToPayFactory->create();
                    $howToPayItem->setBlockTitle($block->getTitle());
                    $howToPayItem->setBlockContent($content);
                    $howToPay[] = $howToPayItem;
                } catch (LocalizedException $e) {

                }

        }

        return $howToPay;
    }
}