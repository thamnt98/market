<?php

namespace SM\GTM\Model\Variable;

use Magento\Email\Model\Template\Filter as ExpressionFilter;
use Magento\Framework\Logger\Monolog as LoggerInterface;

/**
 * Class Processor
 * @package SM\GTM\Model\Variable
 * @deprecated
 */
class Processor
{
    /**
     * @var ExpressionFilter
     */
    private $filter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Processor constructor.
     * @param ExpressionFilter $filter
     */
    public function __construct(
        ExpressionFilter $filter,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->filter = $filter;
    }

    /**
     * @param string $template
     * @param array $variables
     * @return string
     */
    public function processTemplate(string $template, array $variables)
    {
        try {
            /** @TODO: Mock template */
            return $this->filter->setVariables($variables)->filter($template);
        } catch (\Exception $exception) {
            $this->logger->error('Error during parsing data from GTM Template.');
            $this->logger->crit($exception->getMessage());
            $this->logger->debug(sprintf('Template: %s', $template));
            $this->logger->debug(sprintf('Variables: %s', \Zend_Json_Encoder::encode($variables)));
        }

        return '';
    }
}
