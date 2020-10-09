<?php

namespace SM\DigitalProduct\Model;

use Magento\Framework\Api\DataObjectHelper;
use SM\DigitalProduct\Api\Data\Inquire\InquireBpjsDataInterface;
use SM\DigitalProduct\Api\Data\Inquire\InquireBpjsDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Inquire\InquireElectricityPostPaidDataInterface;
use SM\DigitalProduct\Api\Data\Inquire\InquireElectricityPostPaidDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Inquire\InquireElectricityPrePaidDataInterface;
use SM\DigitalProduct\Api\Data\Inquire\InquireElectricityPrePaidDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Inquire\InquireMobilePostpaidDataInterface;
use SM\DigitalProduct\Api\Data\Inquire\InquireMobilePostpaidDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Inquire\InquirePdamDataInterface;
use SM\DigitalProduct\Api\Data\Inquire\InquirePdamDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Inquire\InquireTelkomDataInterface;
use SM\DigitalProduct\Api\Data\Inquire\InquireTelkomDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Inquire\PdamDetailFeeDataInterface;
use SM\DigitalProduct\Api\Data\Inquire\PdamDetailFeeDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\Inquire\PdamDetailUsageDataInterface;
use SM\DigitalProduct\Api\Data\Inquire\PdamDetailUsageDataInterfaceFactory;
use SM\DigitalProduct\Api\InquireRepositoryInterface;
use SM\DigitalProduct\Api\ReorderRepositoryInterface;
use Trans\DigitalProduct\Model\DigitalProductInquire;

/**
 * Class InquireRepository
 * @package SM\DigitalProduct\Model
 */
class InquireRepository implements InquireRepositoryInterface
{

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DigitalProductInquire
     */
    protected $digitalProductInquire;

    /**
     * @var InquireElectricityPrePaidDataInterfaceFactory
     */
    protected $inquireElectricityPrePaidDataFactory;

    /**
     * @var InquireElectricityPostPaidDataInterfaceFactory
     */
    protected $inquireElectricityPostPaidDataFactory;

    /**
     * @var InquirePdamDataInterfaceFactory
     */
    protected $inquirePdamDataFactory;

    /**
     * @var PdamDetailFeeDataInterfaceFactory
     */
    protected $pdamDetailFeeDataFactory;

    /**
     * @var PdamDetailUsageDataInterfaceFactory
     */
    protected $pdamDetailUsageDataFactory;

    /**
     * @var InquireBpjsDataInterfaceFactory
     */
    protected $inquireBpjsDataFactory;

    /**
     * @var InquireMobilePostpaidDataInterfaceFactory
     */
    protected $inquireMobilePostpaidDataFactory;

    /**
     * @var InquireTelkomDataInterfaceFactory
     */
    protected $inquireTelkomDataFactory;

    /**
     * InquireRepository constructor.
     * @param DataObjectHelper $dataObjectHelper
     * @param DigitalProductInquire $digitalProductInquire
     * @param InquireElectricityPostPaidDataInterfaceFactory $inquireElectricityPostPaidDataFactory
     * @param InquireElectricityPrePaidDataInterfaceFactory $inquireElectricityPrePaidDataFactory
     * @param InquirePdamDataInterfaceFactory $inquirePdamDataFactory
     * @param PdamDetailFeeDataInterfaceFactory $pdamDetailFeeDataFactory
     * @param PdamDetailUsageDataInterfaceFactory $pdamDetailUsageDataFactory
     * @param InquireBpjsDataInterfaceFactory $inquireBpjsDataFactory
     * @param InquireMobilePostpaidDataInterfaceFactory $inquireMobilePostpaidDataFactory
     * @param InquireTelkomDataInterfaceFactory $inquireTelkomDataFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        DigitalProductInquire $digitalProductInquire,
        InquireElectricityPostPaidDataInterfaceFactory $inquireElectricityPostPaidDataFactory,
        InquireElectricityPrePaidDataInterfaceFactory $inquireElectricityPrePaidDataFactory,
        InquirePdamDataInterfaceFactory $inquirePdamDataFactory,
        PdamDetailFeeDataInterfaceFactory $pdamDetailFeeDataFactory,
        PdamDetailUsageDataInterfaceFactory $pdamDetailUsageDataFactory,
        InquireBpjsDataInterfaceFactory $inquireBpjsDataFactory,
        InquireMobilePostpaidDataInterfaceFactory $inquireMobilePostpaidDataFactory,
        InquireTelkomDataInterfaceFactory $inquireTelkomDataFactory
    ) {
        $this->inquireTelkomDataFactory = $inquireTelkomDataFactory;
        $this->inquireMobilePostpaidDataFactory = $inquireMobilePostpaidDataFactory;
        $this->inquireBpjsDataFactory = $inquireBpjsDataFactory;
        $this->pdamDetailUsageDataFactory = $pdamDetailUsageDataFactory;
        $this->pdamDetailFeeDataFactory = $pdamDetailFeeDataFactory;
        $this->inquirePdamDataFactory = $inquirePdamDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->digitalProductInquire = $digitalProductInquire;
        $this->inquireElectricityPostPaidDataFactory = $inquireElectricityPostPaidDataFactory;
        $this->inquireElectricityPrePaidDataFactory = $inquireElectricityPrePaidDataFactory;
    }

    private function responseProcess($response, $inquireData, $interface)
    {
        if ($response != null) {
            $this->dataObjectHelper->populateWithArray(
                $inquireData,
                $response,
                $interface
            );
        } else {
            $inquireData
                ->setResponseCode(ReorderRepositoryInterface::ERROR)
                ->setMessage(__("Request timed out"));
        }
        return $inquireData;
    }

    /**
     * @inheritDoc
     */
    public function inquireElectricityPrePaid($customerNumber, $customerId, $productId)
    {
        /** @var InquireElectricityPrePaidDataInterface $inquireElectricityPrePaidData */
        $response = $this->digitalProductInquire->electricity($customerId, $customerNumber, $productId);

        return $this->responseProcess(
            json_decode($response, true),
            $this->inquireElectricityPrePaidDataFactory->create(),
            InquireElectricityPrePaidDataInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function inquireElectricityPostPaid($customerNumber, $customerId, $productId)
    {
        $response = $this->digitalProductInquire->electricityPostpaid($customerId, $customerNumber, $productId);

        /** @var InquireElectricityPostPaidDataInterface $inquireElectricityPostPaidData */
        $inquireElectricityPostPaidData = $this->responseProcess(
            json_decode($response, true),
            $this->inquireElectricityPostPaidDataFactory->create(),
            InquireElectricityPostPaidDataInterface::class
        );

        if ($inquireElectricityPostPaidData != false && !is_null($inquireElectricityPostPaidData->getBills()) &&
            count($inquireElectricityPostPaidData->getBills())
        ) {
            foreach ($inquireElectricityPostPaidData->getBills() as &$bill) {
                $bill->setBillPeriod(date("F Y", strtotime($bill->getBillPeriod() . "01")));
                $bill->setDueDate(date("d F Y", strtotime($bill->getDueDate())));
            }
        }

        return $inquireElectricityPostPaidData;
    }

    /**
     * @inheritDoc
     */
    public function inquirePdam($customerNumber, $customerId, $productId, $operatorCode)
    {
        $response = $this->digitalProductInquire->pdam($customerId, $customerNumber, $productId, $operatorCode);

        $responseArray = json_decode($response, true);

        /** @var InquirePdamDataInterface $inquirePdamData */
        $inquirePdamData = $this->responseProcess(
            $responseArray,
            $inquirePdamData = $this->inquirePdamDataFactory->create(),
            InquirePdamDataInterface::class
        );
        if (!is_null($inquirePdamData->getBills())) {
            foreach ($inquirePdamData->getBills() as $key => &$billDataAsArray) {
                /** @var PdamDetailFeeDataInterface $pdamDetailFeeData */
                $pdamDetailFeeData = $this->responseProcess(
                    $responseArray["bills"][$key]["detail_fee"],
                    $this->pdamDetailFeeDataFactory->create(),
                    PdamDetailFeeDataInterface::class
                );

                /** @var PdamDetailUsageDataInterface $pdamDetailUsageData */
                $pdamDetailUsageData = $this->responseProcess(
                    $responseArray["bills"][$key]["detail_usage"],
                    $this->pdamDetailUsageDataFactory->create(),
                    PdamDetailUsageDataInterface::class
                );

                $billDataAsArray
                    ->setDetailFee($pdamDetailFeeData)
                    ->setDetailUsage($pdamDetailUsageData);
            }
        }

        return $inquirePdamData;
    }

    /**
     * @inheritDoc
     */
    public function inquireBpjs($customerNumber, $customerId, $productId, $paymentPeriod)
    {
        $response = $this->digitalProductInquire->bpjsKesehatan(
            $customerId,
            $customerNumber,
            $paymentPeriod,
            $productId
        );

        return $this->responseProcess(
            json_decode($response, true),
            $this->inquireBpjsDataFactory->create(),
            InquireBpjsDataInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function inquireMobilePostpaid($customerNumber, $customerId, $productId)
    {
        $response = $this->digitalProductInquire->mobilePostpaid(
            $customerId,
            $customerNumber,
            $productId
        );

        return $this->responseProcess(
            json_decode($response, true),
            $this->inquireMobilePostpaidDataFactory->create(),
            InquireMobilePostpaidDataInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function inquireTelkom($customerNumber, $customerId, $productId)
    {
        $response = $this->digitalProductInquire->telkomPostpaid(
            $customerId,
            $customerNumber,
            $productId
        );

        $responseArray = json_decode($response, true);

        /** @var InquireTelkomDataInterface $inquireTelkomData */
        $inquireTelkomData = $this->responseProcess(
            $responseArray,
            $this->inquireTelkomDataFactory->create(),
            InquireTelkomDataInterface::class
        );

        if (isset($responseArray["description"])) {
            $inquireTelkomData->setMessage($responseArray["description"]);
        }

        return $inquireTelkomData;
    }
}
