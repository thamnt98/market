<?php
/**
 * @category Trans
 * @package  Trans_CustomerMyProfile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CustomerMyProfile\Controller\MyProfile;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NotFoundException;

class ProfilePictureView extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * ProfilePictureView constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Url\DecoderInterface $urlDecoder
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Url\DecoderInterface $urlDecoder,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->urlDecoder = $urlDecoder;
        $this->fileFactory = $fileFactory;
        return parent::__construct($context);
    }

    /**
     * View action
     *
     * @return \Magento\Framework\View\Result\PageFactory
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $file = null;
        if ($this->getRequest()->getParam('image')) {
            // show plain image
            $file = $this->urlDecoder->decode($this->getRequest()->getParam('image'));
            $plain = true;
        } else {
            throw new NotFoundException(__('Page not found.'));
        }

        /** @var \Magento\Framework\Filesystem $filesystem */
        $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
        $directory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER . '/' . ltrim($file, '/');
        $path = $directory->getAbsolutePath($fileName);

        if (!$directory->isFile($fileName)
            && !$this->_objectManager->get('Magento\MediaStorage\Helper\File\Storage')->processStorageFile($path)
        ) {
            throw new NotFoundException(__('Page not found.'));
        }

        if ($plain) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'gif':
                    $contentType = 'image/gif';
                    break;
                case 'jpg':
                    $contentType = 'image/jpeg';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    break;
                default:
                    $contentType = 'application/octet-stream';
                    break;
            }
            $stat = $directory->stat($fileName);
            $contentLength = $stat['size'];
            $contentModify = $stat['mtime'];

            /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $contentLength)
                ->setHeader('Last-Modified', date('r', $contentModify));
            $resultRaw->setContents($directory->readFile($fileName));
            return $resultRaw;
        } else {
            $name = pathinfo($path, PATHINFO_BASENAME);
            $this->fileFactory->create(
                $name,
                ['type' => 'filename', 'value' => $fileName],
                DirectoryList::MEDIA
            );
        }
    }
}
