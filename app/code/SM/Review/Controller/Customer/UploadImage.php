<?php


namespace SM\Review\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use SM\Review\Model\Upload\ImageUploader;

class UploadImage extends \Magento\Framework\App\Action\Action
{

    /**
     * @var ImageUploader
     */
    private $ImageUploader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;


    /**
     * @param Context $context
     * @param ImageUploader $ImageUploader
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ImageUploader $ImageUploader

    ) {
        $this->storeManager        = $storeManager;
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
        $result = $this->ImageUploader->saveImageToMediaFolder('image');
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
