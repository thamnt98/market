<?php


namespace SM\CustomPrice\Plugin\Framework\Search\Request\Config;


use SM\CustomPrice\Model\Search\RequestGenerator;

class FilesystemReader
{
    /**
     * @var RequestGenerator
     */
    protected $requestGenerator;

    /**
     * FilesystemReader constructor.
     * @param RequestGenerator $requestGenerator
     */
    public function __construct(
        RequestGenerator $requestGenerator
    ) {
        $this->requestGenerator = $requestGenerator;
    }

    /**
     * @param \Magento\Framework\Config\ReaderInterface $subject
     * @param array $requests
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormatParameter)
     */
    public function afterRead(\Magento\Framework\Config\ReaderInterface $subject, $requests)
    {
        return array_merge_recursive($requests, $this->requestGenerator->generate());
    }
}
