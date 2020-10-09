<?php

namespace SM\Installation\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use SM\Installation\Helper\Data as InstallationHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;

class InstallationRepository implements \SM\Installation\Api\InstallationRepositoryInterface
{
    const QUOTE_OPTION_KEY = 'installation_service';
    const USED_FIELD_NAME = 'is_installation';
    const NOTE_FIELD_NAME = 'installation_note';
    const FEE_FIELD_NAME  = 'installation_fee';

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var InstallationHelper
     */
    protected $helper;

    protected $productRepository;

    /**
     * Update constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param InstallationHelper $helper
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        InstallationHelper $helper,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param int $cartId
     * @param int $itemId
     * @param string $action
     * @param int $useInstallation
     * @param string $installationNote
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Json_Exception
     * @throws \Exception
     */
    public function save($cartId, $itemId, $action, $useInstallation, $installationNote)
    {
        try {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            $item = $this->getQuote($cartId)->getItemById($itemId);
            $productId = $item->getProduct()->getId();
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->productRepository->getById($productId);
            if ($this->helper->isEnabled() && $product->getIsService()) {
                switch ($action) {
                    case 'remove':
                        $this->removeInstallationItem($item);
                        break;
                    case 'update':
                        $this->updateInstallationItem($item, $useInstallation, $installationNote);
                        break;
                }
                $item->saveItemOptions();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $quoteId
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getQuote($quoteId)
    {
        return $this->quoteRepository->get($quoteId);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     * @param $useInstallation
     * @param $installationNote
     * @throws \Zend_Json_Exception
     */
    public function updateInstallationItem(&$item, $useInstallation, $installationNote)
    {
        $data = [
            self::QUOTE_OPTION_KEY => [
                self::USED_FIELD_NAME => $useInstallation,
                self::NOTE_FIELD_NAME => trim($installationNote),
            ]
        ];
        $option = $item->getOptionByCode('info_buyRequest');
        if ($option) {
            $value = \Zend_Json_Decoder::decode($option->getValue());
            $value = array_merge($value, $data);
            $option->setValue(\Zend_Json_Encoder::encode($value, true));
        } else {
            $option = [
                'code' => 'info_buyRequest',
                'value' => \Zend_Json_Encoder::encode($data, true)
            ];
        }

        $item->addOption($option);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     *
     * @throws \Zend_Json_Exception
     */
    public function removeInstallationItem(&$item)
    {
        $option = $item->getOptionByCode('info_buyRequest');
        if ($option) {
            $value = \Zend_Json_Decoder::decode($option->getValue());
            unset($value[self::QUOTE_OPTION_KEY]);
            $option->setValue(\Zend_Json_Encoder::encode($value, true));
        }

        $item->addOption($option);
    }
}
