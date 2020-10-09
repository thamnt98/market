<?php

declare(strict_types=1);

namespace SM\Reports\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class LatestViewed
 * @package SM\Reports\Controller\Ajax
 */
class LatestViewedProduct extends Action
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
