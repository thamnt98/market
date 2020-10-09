<?php

namespace SM\MobileApi\Model\Data\ProductLabel;

use Amasty\Label\Model\AbstractLabels;
use Magento\Framework\DataObject\IdentityInterface;
use SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface;

class Labels extends AbstractLabels implements ProductLabelInterface, IdentityInterface
{
    /**
     * @var array
     */
    private $horisontalPositions = ['left', 'center', 'right'];

    /**
     * @var array
     */
    private $verticalPositions   = ['top', 'middle', 'bottom'];

    /**
     * @return int
     */
    public function getAppliedProductId()
    {
        return $this->getParentProduct() ? $this->getParentProduct()->getId() : $this->getProduct()->getId();
    }

    /**
     * @return string
     */
    public function getImageSrc()
    {
        return $this->helper->getImageUrl($this->getValue('img'));
    }

    /**
     * combine all variation of label position.
     *
     * @param bool $asText
     *
     * @return array
     */
    public function getAvailablePositions($asText = true)
    {
        $a = [];
        foreach ($this->verticalPositions as $first) {
            foreach ($this->horisontalPositions as $second) {
                $a[] = $asText ?
                    __(ucwords($first . ' ' . $second))
                    :
                    $first . '-' . $second;
            }
        }

        return $a;
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return array_merge(
            ($this->getProduct() instanceof IdentityInterface) ? $this->getProduct()->getIdentities() : [],
            [self::CACHE_TAG . '_' . $this->getId()]
        );
    }

    /**
     * @return AbstractLabels|void
     * @throws \Exception
     */
    public function afterDeleteCommit()
    {
        parent::afterDeleteCommit();

        if ($catFile = $this->getCatImg()) {
            $this->deleteFile($catFile);
        }

        if ($prodFile = $this->getProdImg()) {
            $this->deleteFile($prodFile);
        }
        return $this;
    }

    /**
     * @param string $file
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function deleteFile($file)
    {
        $path = $this->helper->getImagePath($file);
        if ($this->file->isExists($path)) {
            $this->file->deleteFile($path);
        }
    }

    /**
     * Get position value of label
     * @return string
     */
    public function getCssClass()
    {
        $all = $this->getAvailablePositions(false);
        $pos = $this->getValue('pos') ? $this->getValue('pos') : 0;

        return $all[$pos];
    }

    /**
     * @return array|mixed|string
     */
    public function getStoreIds()
    {
        $storeIds = $this->getStores();
        $storeIds = explode(',', $storeIds);

        return $storeIds;
    }

    /**
     * Get label text with replacing data
     * @return string
     */
    public function getText()
    {
        $txt = $this->getValue('txt');

        preg_match_all('/{([a-zA-Z:\_0-9]+)}/', $txt, $vars);
        if (!$vars[1]) {
            return $txt;
        }
        $vars = $vars[1];
        $product = $this->getProduct();

        foreach ($vars as $var) {
            $value = $this->getCustomVariable($var, $product);
            $txt = str_replace('{' . $var . '}', $value, $txt);
        }

        return $txt;
    }

    /**
     * @param $var
     * @param $product
     * @return string
     */
    private function getCustomVariable($var, $product)
    {
        switch ($var) {
            case 'PRICE':
                $price = $this->loadPrices();
                $value = $this->convertPrice($price['price']);
                break;
            case 'SPECIAL_PRICE':
                $price = $this->loadPrices();
                $value = $this->convertPrice($price['special_price']);
                break;
            case 'FINAL_PRICE':
                $value = $this->convertPrice(
                    $this->catalogData->getTaxPrice($product, $product->getFinalPrice(), false)
                );
                break;
            case 'FINAL_PRICE_INCL_TAX':
                $value = $this->convertPrice(
                    $this->catalogData->getTaxPrice($product, $product->getFinalPrice(), true)
                );
                break;
            case 'STARTINGFROM_PRICE':
                $value = $this->convertPrice($this->getMinimalPrice($product));
                break;
            case 'STARTINGTO_PRICE':
                $value = $this->convertPrice($this->getMaximalPrice($product));
                break;
            case 'SAVE_AMOUNT':
                $price = $this->loadPrices();
                $value = $this->convertPrice($price['price'] - $price['special_price']);
                break;
            case 'SAVE_PERCENT':
                $value = $this->getPercentPrice();
                break;
            case 'BR':
                $value = '<br/>';
                break;
            case 'SKU':
                $value = $product->getSku();
                break;
            case 'NEW_FOR':
                $createdAt = strtotime($product->getCreatedAt());
                $value     = max(1, floor((time() - $createdAt) / 86400));
                break;
            case 'STOCK':
                $value = $this->getProductQty($product);
                break;
            case 'SPDL':
                $value = 0;
                $toDate = $product->getSpecialToDate();
                if ($toDate) {
                    $currentTime = $this->date->date();

                    $diff = strtotime($toDate) - strtotime($currentTime);
                    if ($diff >= 0) {
                        $value = floor($diff / (60*60*24));//days
                    }
                }
                break;
            case 'SPHL':
                $value = 0;
                $toDate = $product->getSpecialToDate();
                if ($toDate) {
                    $currentTime = $this->date->date();

                    $diff = strtotime($toDate) - strtotime($currentTime);
                    if ($diff >= 0) {
                        $value = floor($diff / (60*60));//hours
                    }
                }
                break;
            default:
                $value = $this->getDefaultValue($product, $var);
        }

        return $value;
    }

    /**
     * @return float|int
     */
    protected function getPercentPrice()
    {
        $value = 0;
        $price = $this->loadPrices();
        if ($price['price'] != 0) {
            $value = $price['price'] - $price['special_price'];
            switch ($this->helper->getModuleConfig('on_sale/rounding')) {
                case 'floor':
                    $value = floor($value * 100 / $price['price']);
                    break;
                case 'ceil':
                    $value = ceil($value * 100 / $price['price']);
                    break;
                case 'round':
                default:
                    $value = round($value * 100 / $price['price']);
                    break;
            }
        }

        return $value;
    }

    /**
     * Strip tag from price and convert it to store format
     *
     * @param $price
     *
     * @return string
     */
    private function convertPrice($price)
    {
        $store = $this->storeManager->getStore();
        return strip_tags($this->priceCurrency->convertAndFormat($price, $store));
    }

    /**
     * @param $product
     * @param $var
     *
     * @return string
     */
    private function getDefaultValue($product, $var)
    {
        $str = 'ATTR:';
        $value = '';
        if (substr($var, 0, strlen($str)) == $str) {
            $code  = trim(substr($var, strlen($str)));

            $decimal = null;
            if (false !== strpos($code, ':')) {
                $temp = explode(':', $code);
                $code = $temp[0];
                $decimal = $temp[1];
            }

            $attribute = $product->getResource()->getAttribute($code);
            if ($attribute && in_array($attribute->getFrontendInput(), ['select', 'multiselect'])) {
                $value = $product->getAttributeText($code);
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
            } else {
                $value = $product->getData($code);
            }

            if ($decimal !== null && false !== strpos($value, '.')) {
                $temp = explode('.', $value);
                $value = $temp[0] . '.' . substr($temp[1], 0, $decimal);
            }

            if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $value)) {
                $value = $this->timezone->formatDateTime(
                    new \DateTime($value),
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::NONE
                );
            }
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        $style = $this->getValue('style');
        $size = $this->getImageInfo();
        if ($size && array_key_exists('w', $size)) {
            $style = 'max-width: ' . $size['w'] . '; ' . $style;
        }

        return $style;
    }

    /**
     * @return array
     */
    private function getImageInfo()
    {
        $path = $this->getValue('img');
        $path = $this->helper->getImagePath($path);
        if ($path) {
            try {
                if (strpos($path, 'svg') !== false) {
                    $xml = simplexml_load_file($path);
                    $attr = $xml->attributes();
                    $info = [(int)$attr->width . 'pt', (int)$attr->height . 'pt'];
                } else {
                    $info = getimagesize($path);
                    $info[0] .= 'px';
                    $info[1] .= 'px';
                }
            } catch (\Exception $ex) {
                return [];
            }
        } else {
            return [];
        }

        return ['w'=>$info[0], 'h'=>$info[1]];
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        return $this->_getData(ProductLabelInterface::STORES);
    }

    /**
     * {@inheritdoc}
     */
    public function setStores($stores)
    {
        $this->setData(ProductLabelInterface::STORES, $stores);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductText()
    {
        return $this->_getData(ProductLabelInterface::PRODUCT_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductText($prodTxt)
    {
        $this->setData(ProductLabelInterface::PRODUCT_TEXT, $prodTxt);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTextColor()
    {
        return $this->_getData(ProductLabelInterface::PRODUCT_TEXT_COLOR);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductTextColor($prodStyle)
    {
        $this->setData(ProductLabelInterface::PRODUCT_TEXT_COLOR, $prodStyle);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductBackGround()
    {
        return $this->_getData(ProductLabelInterface::PRODUCT_BACKGROUND_COLOR);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductBackGround($prodTextStyle)
    {
        $this->setData(ProductLabelInterface::PRODUCT_BACKGROUND_COLOR, $prodTextStyle);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategoryText()
    {
        return $this->getData(self::CATEGORY_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setCategoryText($value)
    {
        return $this->setData(self::CATEGORY_TEXT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCategoryBackGround()
    {
        return $this->getData(self::CATEGORY_BACKGROUND_COLOR);
    }

    /**
     * @inheritDoc
     */
    public function setCategoryBackGround($value)
    {
        return $this->setData(self::CATEGORY_BACKGROUND_COLOR, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCategoryTextColor()
    {
        return $this->getData(self::CATEGORY_TEXT_COLOR);
    }

    /**
     * @inheritDoc
     */
    public function setCategoryTextColor($value)
    {
        return $this->setData(self::CATEGORY_TEXT_COLOR, $value);
    }
}
