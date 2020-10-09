<?php

declare(strict_types=1);

namespace SM\Email\Api\Repository;

use Magento\Email\Model\Template;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface EmailTemplateRepositoryInterface
 * @package SM\Email\Api
 */
interface EmailTemplateRepositoryInterface
{
    /**
     * @param Template $template
     * @return Template
     * @throws CouldNotSaveException
     */
    public function save(Template $template): Template;
}
