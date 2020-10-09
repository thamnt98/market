<?php
declare(strict_types=1);

namespace SM\GTM\Model\Data\Collectors;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use SM\GTM\Api\CollectorInterface;
use SM\GTM\Api\MapperInterface;

/**
 * Class Quote
 * @package SM\GTM\Model\Data\Collectors
 */
class Quote implements CollectorInterface
{
    /**
     * @var CartInterface
     */
    private $quote;

    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Quote constructor.
     * @param MapperInterface $mapper
     * @param Session $checkoutSession
     */
    public function __construct(
        MapperInterface $mapper,
        Session $checkoutSession
    ) {
        $this->mapper = $mapper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param CartInterface $cart
     * @return $this
     */
    public function setQuote(CartInterface $cart)
    {
        $this->quote = $cart;
        return $this;
    }

    /**
     * @return CartInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuote()
    {
        if (!$this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        $this->quote->setItemsQty((int)$this->quote->getItemsQty());
        return $this->quote;
    }

    /**
     * @inheritDoc
     */
    public function collect()
    {
        try {
            return $this->mapper->map($this->getQuote())->toArray();
        } catch (\Exception $exception) {
            return [];
        }
    }
}
