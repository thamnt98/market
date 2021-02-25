<?php 
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Block\Checkout;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Model\Order;
use SM\Checkout\Helper\Config as CheckoutConfigHelper;
use SM\Checkout\Helper\Payment;
use SM\Sales\Model\ParentOrderRepository;
use Trans\LocationCoverage\Model\CityRepository;
use Trans\LocationCoverage\Model\DistrictRepository;
use Trans\Sprint\Helper\Config as SprintHelper;
use SM\Checkout\Block\Checkout\Success;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Framework\App\Http\Context as HttpContext;
use Trans\Sprint\Api\SprintResponseRepositoryInterface;
use Trans\Sprint\Api\SprintPaymentFlagRepositoryInterface;
use Trans\Sprint\Helper\Config;
use Trans\Sprint\Helper\Data as SprintHelperData;
use Magento\Framework\Pricing\Helper\Data as PriceHelperData;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Catalog\Helper\Image;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;


class TransmartSuccess extends Success
{

  /**
   * @var \Trans\Mepay\Helper\Payment\Transaction
   */
  protected $transactionHelper;

  /**
   * @var \Trans\Mepay\Model\Payment\Status
   */
  protected $statusCheck;

  /**
   * Constructor
   * @param Context $context
   * @param Session $checkoutSession
   * @param OrderConfig $orderConfig
   * @param HttpContext $httpContext
   * @param SprintResponseRepositoryInterface $sprintResponseRepository
   * @param SprintPaymentFlagRepositoryInterface $sprintPaymentFlagRepository
   * @param Config $config
   * @param SprintHelper  $paymentLogo
   * @param SprintHelperData $sprintHelperData
   * @param PriceHelperData priceHelper
   * @param DateTime  $date
   * @param TimezoneInterface $timezone
   * @param SourceRepositoryInterface $sourceRepository
   * @param CheckoutConfigHelper $checkoutConfigHelper
   * @param Image $image
   * @param CityRepository $cityRepository
   * @param DistrictRepository $districtRepository
   * @param Payment $paymentHelper
   * @param CollectionFactory $orderCollectionFactory
   * @param array $data
   */
  public function __construct (
      \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $couponCollFact,
    Context $context,
    Session $checkoutSession,
    OrderConfig $orderConfig,
    HttpContext $httpContext,
    SprintResponseRepositoryInterface $sprintResponseRepository,
    SprintPaymentFlagRepositoryInterface $sprintPaymentFlagRepository,
    Config $config,
    SprintHelper $paymentLogo,
    SprintHelperData $sprintHelperData,
    PriceHelperData $priceHelper,
    DateTime $date,
    TimezoneInterface $timezone,
    SourceRepositoryInterface $sourceRepository,
    CheckoutConfigHelper $checkoutConfigHelper,
    Image $image,
    CityRepository $cityRepository,
    DistrictRepository $districtRepository,
    Payment $paymentHelper,
    CollectionFactory $orderCollectionFactory,
    array $data = []
  ) {
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $this->transactionHelper = $objectManager->create('Trans\Mepay\Helper\Payment\Transaction');
    $this->statusCheck = $objectManager->create('Trans\Mepay\Model\Payment\Status');
    parent::__construct(
        $couponCollFact,
      $context,
      $checkoutSession,
      $orderConfig,
      $httpContext,
      $sprintResponseRepository,
      $sprintPaymentFlagRepository,
      $config,
      $paymentLogo,
      $sprintHelperData,
      $priceHelper,
      $date,
      $timezone,
      $sourceRepository,
      $checkoutConfigHelper,
      $image,
      $cityRepository,
      $districtRepository,
      $paymentHelper,
      $orderCollectionFactory,
      $data
    );
  }

  /**
   * Is payment success
   * @return boolean
   */
  public function isSucceed()
    {
        $paymentMethod = $this->getPaymentMethod();

        if ($paymentMethod == 'trans_mepay_va') {
          return $this->isCaptured();
        }
        if ($paymentMethod == 'trans_mepay_cc') {
          return $this->isCaptured();
        }
        if ($paymentMethod == 'trans_mepay_allbankccdebit') {
          return $this->isCaptured();
        }
        if ($paymentMethod == 'trans_mepay_qris') {
          return $this->isCaptured();
        }

        if ($paymentMethod == 'trans_mepay_debit') {
          return $this->isCaptured();
        }

        if ($paymentMethod == 'trans_mepay_allbank_debit') {
          return $this->isCaptured();
        }

        if ($paymentMethod == 'trans_mepay_allbank_cc') {
          return $this->isCaptured();
        }

        return $this->paymentHelper->isCredit($paymentMethod) || $this->paymentHelper->isInstallment($paymentMethod) || ($this->paymentHelper->isVirtualAccount($paymentMethod) && $this->isPaid());
    }

    public function isPending()
    {
      $txn = $this->transactionHelper->getLastOrderTransaction($this->order->getId());
      $txnData = $txn->getData();
      if (isset($txnData['txn_type'])) {
        if ($this->statusCheck->isAuthorize($txnData['txn_type'])) {
          return true;
        }
      }
      return false;

    }

    /**
     * Is order has capture
     * @return boolean
     */
    public function isCaptured()
    {
      $txn = $this->transactionHelper->getLastOrderTransaction($this->order->getId());
      $txnData = $txn->getData();
      if (isset($txnData['txn_type'])) {
        if ($this->statusCheck->isCapture($txnData['txn_type'])) {
          return true;
        }
      }
      return false;
    }
}
