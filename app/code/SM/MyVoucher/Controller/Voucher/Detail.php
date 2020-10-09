<?php
namespace SM\MyVoucher\Controller\Voucher;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Detail extends \Magento\Customer\Controller\AbstractAccount implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var \SM\MyVoucher\Model\RuleRepository
     */
    protected $couponRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\Registry                          $registry
     * @param \SM\MyVoucher\Model\RuleRepository                   $couponRepository
     * @param \Magento\Customer\Model\Session                      $session
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Framework\App\Action\Context                $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \SM\MyVoucher\Model\RuleRepository $couponRepository,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->redirectFactory = $redirectFactory;
        $this->couponRepository = $couponRepository;
        $this->session = $session;
        $this->registry = $registry;
    }


    public function execute()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $voucher = $this->couponRepository->getVoucherDetailByCustomer(
                    $this->session->getCustomerId(),
                    $id
                );

                if ($voucher->getId() && $voucher->getId() == $id) {
                    $this->registry->register('voucher', $voucher, true);

                    return $this->resultPageFactory->create();
                } else {
                    $this->messageManager->addErrorMessage('Voucher doesn\'t exists.');
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage('Voucher doesn\'t exists.');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage('Can not get voucher data.');
            }
        }

        return $this->redirectFactory->create()->setPath('myvoucher/voucher');
    }
}