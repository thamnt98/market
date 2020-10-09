<?php


namespace SM\Help\Controller\ContactUs;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use SM\Help\Helper\Email;
use SM\Help\Model\Upload\ImageUploader;

class SendEmail extends Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Email
     */
    protected $helperEmail;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * @param Email $helperEmail
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Session $customerSession
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Email $helperEmail,
        Context $context,
        JsonFactory $resultJsonFactory,
        Session $customerSession,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        ImageUploader $imageUploader
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperEmail = $helperEmail;
        $this->customerSession = $customerSession;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->imageUploader = $imageUploader;
        parent::__construct($context);
    }

    /**
     * Send help email
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        try {
            $dataPost['data'] = $this->getRequest()->getParams();
            $customer = $this->customerSession->getCustomerData();

            if ($dataPost['data']['help_messages'] == 'Yes' && isset($dataPost['data']['store_id'])) {
                if ($customer->getEmail()) {
                    $this->helperEmail->sendEmail($dataPost['data']['store_id'], $customer->getEmail());
                }
            }

            $images = [];
            if (isset($dataPost['data']['images'])) {
                foreach ($dataPost['data']['images'] as $image) {
                    $imagePart = json_decode($image, true);
                    $images[] = $imagePart;
                }
                $dataPost['data']['images'] = $images;
            }

            $this->_eventManager->dispatch(
                'trans_help_contact_submit_after',
                [$dataPost, 'customer' => $customer]
            );

            return $resultJson->setData('true');
        } catch (LocalizedException $e) {
            return $resultJson->setData('false');
        }
    }

    /**
     * @return array|bool
     */
    public function saveFile()
    {
        try {
            $varDirectory = $this->filesystem->getDirectoryWrite(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            );
            /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
            $uploader = $this->uploaderFactory->create(['fileId' => 'file']);
            $workingDir = $varDirectory->getAbsolutePath('contact/');
            $result = $uploader->save($workingDir);
            if ($result['file']) {
                return $result;
            }
        } catch (\Exception $exception) {
            return false;
        }

        return false;
    }
}
