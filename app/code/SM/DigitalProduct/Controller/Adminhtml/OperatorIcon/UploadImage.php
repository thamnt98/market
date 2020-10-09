<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Controller\Adminhtml\Category
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Controller\Adminhtml\OperatorIcon;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\FileManagement\Model\ImageUploader;

/**
 * Class UploadImage
 * @package SM\DigitalProduct\Controller\Adminhtml\Category
 */
class UploadImage extends Action
{
    const TMP_PATH = "sm/dp/operator/tmp";
    const BASE_PATH = "sm/dp/operator";
    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * UploadImage constructor.
     * @param Context $context
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
        $this->imageUploader->setBasePath(self::BASE_PATH);
        $this->imageUploader->setBaseTmpPath(self::TMP_PATH);

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $result = $this->imageUploader->saveFileToTmpDir("icon");
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
