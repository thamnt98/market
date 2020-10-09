<?php

namespace SM\FlashSale\Controller\Adminhtml\Catalog\Event;

use Magento\Backend\App\Action\Context;
use Magento\CatalogEvent\Model\EventFactory;
use Magento\Framework\Stdlib\DateTime\Filter\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Save extends \Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event\Save{

    protected $cacheTypeList;
    protected $cacheFrontendPool;

    public function __construct(Context $context,
                                Registry $coreRegistry,
                                EventFactory $eventFactory,
                                DateTime $dateTimeFilter,
                                StoreManagerInterface $storeManager,
                                TypeListInterface $cacheTypeList,
                                Pool $cacheFrontendPool)
    {
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($context, $coreRegistry, $eventFactory, $dateTimeFilter, $storeManager);
    }


    /**
     * Save action
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $event = $this->_eventFactory->create()->setStoreId($this->getRequest()->getParam('store', 0));
        $eventId = $this->getRequest()->getParam('id', false);
        if ($eventId) {
            $event->load($eventId);
        } else {
            $event->setCategoryId($this->getRequest()->getParam('category_id'));
        }

        $postData = $this->_filterPostData($this->getRequest()->getPostValue());

        if (!isset($postData['catalogevent'])) {
            $this->messageManager->addError(__('Something went wrong while saving this event.'));
            $this->_redirect('adminhtml/*/edit', ['_current' => true]);
            return;
        }

        $data = new \Magento\Framework\DataObject($postData['catalogevent']);

        /** @var \Magento\CatalogEvent\Model\DateResolver $dateResolver */
        $dateResolver = $this->_objectManager->get(\Magento\CatalogEvent\Model\DateResolver::class);

        $event->setDisplayState(
            $data->getDisplayState()
        )->setStoreDateStart(
            $dateResolver->convertDate($data->getDateStart())
        )->setStoreDateEnd(
            $dateResolver->convertDate($data->getDateEnd())
        )->setSortOrder(
            $data->getSortOrder()
        )->setTermsConditions(
            $data->getData('terms_conditions')
        )->setMbShortTitle(
            $data->getData('mb_short_title')
        )->setMbTitle(
            $data->getData('mb_title')
        )->setMbShortDescription(
            $data->getData('mb_short_description')
        )->applyStatusByDates();


        $flashImage = $this->getRequest()->getFiles('flash_sale_image');
        $fileName = ($flashImage && array_key_exists('name', $flashImage)) ? $flashImage['name'] : null;
        if ($flashImage && $fileName) {
            try {
                $uploader = $this->_objectManager->create(
                    \Magento\MediaStorage\Model\File\Uploader::class,
                    ['fileId' => 'flash_sale_image']
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

                $result = $uploader->save(
                    $mediaDirectory
                        ->getAbsolutePath('flashsale/image')
                );
                $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $event->setFlashSaleImage($url . 'flashsale/image' . $result['file']);
            } catch (\Exception $e) {
                if ($e->getCode() == 0) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        }
        if (array_key_exists('flash_sale_image', $postData['catalogevent']) && is_array($postData['catalogevent']['flash_sale_image']) && array_key_exists('delete', $postData['catalogevent']['flash_sale_image'])) {
            $event->setFlashSaleImage('');
        }


        $isUploaded = true;
        try {
            $uploader = $this->_objectManager->create(
                \Magento\MediaStorage\Model\File\Uploader::class,
                ['fileId' => 'image']
            );
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(false);
        } catch (\Exception $e) {
            $isUploaded = false;
        }

        $validateResult = $event->validate();
        if ($validateResult !== true) {
            foreach ($validateResult as $errorMessage) {
                $this->messageManager->addError($errorMessage);
            }
            $this->_getSession()->setEventData($event->getData());
            $this->_redirect('adminhtml/*/edit', ['_current' => true]);
            return;
        }

        try {
            if ($data->getData('image/is_default')) {
                $event->setImage(null);
            } elseif ($data->getData('image/delete')) {
                $event->setImage('');
            } elseif ($isUploaded) {
                try {
                    $event->setImage($uploader);
                } catch (\Exception $e) {
                    throw new LocalizedException(__('We did not upload your image.'));
                }
            }
            $event->save();

            $_types = [
                'block_html',
                'full_page'
            ];

            foreach ($_types as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            foreach ($this->cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }

            $this->messageManager->addSuccess(__('You saved the event.'));
            if ($this->getRequest()->getParam('back') == 'edit') {
                $this->_redirect('adminhtml/*/edit', ['_current' => true, 'id' => $event->getId()]);
            } else {
                $this->_redirect('adminhtml/*/');
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_getSession()->setEventData($event->getData());
            $this->_redirect('adminhtml/*/edit', ['_current' => true]);
        }
    }
}
