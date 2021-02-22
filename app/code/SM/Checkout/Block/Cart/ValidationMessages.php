<?php


namespace SM\Checkout\Block\Cart;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;

/**
 * Shopping cart validation messages block
 *
 * @api
 * @since 100.0.2
 */
class ValidationMessages extends \Magento\Framework\View\Element\Messages
{
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $currency;

    /**
     * @var \Magento\Quote\Model\Quote\Validator\MinimumOrderAmount\ValidationMessage
     */
    private $minimumAmountErrorMessage;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Message\Factory $messageFactory
     * @param \Magento\Framework\Message\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param InterpretationStrategyInterface $interpretationStrategy
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\Locale\CurrencyInterface $currency
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\Locale\CurrencyInterface $currency,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );
        $this->cartHelper = $cartHelper;
        $this->currency = $currency;
    }

    /**
     * @return \Magento\Checkout\Block\Cart\ValidationMessages
     */
    protected function _prepareLayout()
    {
        if ($this->cartHelper->getItemsCount()) {
            $this->addQuoteMessages();
            $this->addMessages($this->messageManager->getMessages(true));
        }
        return parent::_prepareLayout();
    }

    /**
     * Add quote messages
     *
     * @return void
     */
    protected function addQuoteMessages()
    {
        // Compose array of messages to add
        $messages = [];
        /** @var \Magento\Framework\Message\MessageInterface $message */
        foreach ($this->cartHelper->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setText($this->escapeHtml($message->getText()));
                $messages[] = $message;
            }
        }

        if ($messages) {
            $this->messageManager->addUniqueMessages($messages);
        }
    }
}