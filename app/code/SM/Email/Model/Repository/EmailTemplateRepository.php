<?php

declare(strict_types=1);

namespace SM\Email\Model\Repository;

use Magento\Email\Model\ResourceModel\Template as ResourceModel;
use Magento\Email\Model\Template;
use Magento\Framework\Exception\CouldNotSaveException;
use SM\Email\Api\Repository\EmailTemplateRepositoryInterface;

/**
 * Class EmailTemplateRepository
 * @package SM\Email\Model
 */
class EmailTemplateRepository implements EmailTemplateRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * EmailTemplateRepository constructor.
     * @param ResourceModel $resourceModel
     */
    public function __construct(
        ResourceModel $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
    }

    /**
     * @inheritDoc
     */
    public function save(Template $template): Template
    {
        try {
            $this->resourceModel->save($template);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save email template: %1', $e->getMessage()));
        }
        return $template;
    }
}
