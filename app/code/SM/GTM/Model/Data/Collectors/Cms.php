<?php

namespace SM\GTM\Model\Data\Collectors;

use Magento\Cms\Api\Data\PageInterface;
use SM\GTM\Api\CollectorInterface;
use SM\GTM\Api\MapperInterface;

/**
 * Class Cms
 * @package SM\GTM\Model\Data\Collectors
 */
class Cms implements CollectorInterface
{
    /**
     * @var MapperInterface
     */
    private $cmsMapper;

    /**
     * @var PageInterface
     */
    private $cmsPage;

    /**
     * Cms constructor.
     * @param MapperInterface $cmsMapper
     * @param PageInterface $cmsPage
     */
    public function __construct(
        MapperInterface $cmsMapper,
        PageInterface $cmsPage
    ) {
        $this->cmsPage = $cmsPage;
        $this->cmsMapper = $cmsMapper;
    }

    /**
     * @inheritDoc
     */
    public function collect()
    {
        return $this->cmsMapper->map($this->cmsPage)->toArray();
    }
}
