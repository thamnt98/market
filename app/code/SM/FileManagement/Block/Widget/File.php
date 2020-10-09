<?php
/**
 * Class File
 * @package SM\FileManagement\Block\Widget
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\FileManagement\Block\Widget;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use SM\FileManagement\Api\FileRepositoryInterface;

class File extends Template implements BlockInterface
{
    /**
     * @var FileRepositoryInterface
     */
    private $fileRepository;

    /**
     * File constructor.
     * @param Template\Context $context
     * @param FileRepositoryInterface $fileRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        FileRepositoryInterface $fileRepository,
        array $data = []
    ) {
        $this->fileRepository = $fileRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return Template|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        if ($fileId = $this->getData('file_id')) {
            try {
                $file = $this->fileRepository->get($fileId);
                $this->setFileUrl($this->getMediaUrl() . $file->getFilePath());
                $this->setThumbnailUrl($this->getMediaUrl() . $file->getThumbnailPath());
            } catch (NoSuchEntityException $e) {
            }
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getMediaUrl()
    {
        return $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
