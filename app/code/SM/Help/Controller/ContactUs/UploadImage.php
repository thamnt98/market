<?php

namespace SM\Help\Controller\ContactUs;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use SM\Help\Model\Upload\ImageUploader;

class UploadImage extends \Magento\Framework\App\Action\Action
{

    /**
     * @var ImageUploader
     */
    private $ImageUploader;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;


    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ImageUploader $ImageUploader
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ImageUploader $ImageUploader

    ) {
        $this->storeManager  = $storeManager;
        $this->ImageUploader = $ImageUploader;
        parent::__construct($context);
    }

    /**
     * Image upload action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->ImageUploader->saveImageToMediaFolder('contactUsImage');
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
