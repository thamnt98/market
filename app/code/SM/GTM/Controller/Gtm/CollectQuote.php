<?php

namespace SM\GTM\Controller\Gtm;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use SM\GTM\Model\Data\Collectors\Quote as QuoteCollectors;

class CollectQuote extends Action
{
    /**
     * @var QuoteCollectors
     */
    private $quoteCollectors;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param QuoteCollectors $quoteCollectors
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        QuoteCollectors $quoteCollectors
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->quoteCollectors = $quoteCollectors;
        parent::__construct($context);
    }

    /**
     * Collect quote data
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        try {
            $quoteData = $this->quoteCollectors->collect();
            $resultJson->setData(['quote' => $quoteData]);
            return $resultJson;
        } catch (\Exception $exception) {
            return $resultJson->setData(['quote' => []]);
        }
    }
}
