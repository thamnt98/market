<?php
namespace SM\CustomerGraphQl\Plugin\Model\Customer;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepo;

class ExtractCustomerData
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var AssetRepo
     */
    protected $assetRepo;

    /**
     * ExtractCustomerData constructor.
     * @param UrlInterface $urlBuilder
     * @param AssetRepo $assetRepo
     */
    public function __construct(
        UrlInterface $urlBuilder,
        AssetRepo $assetRepo
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->assetRepo = $assetRepo;
    }

    /**
     * @param \Magento\CustomerGraphQl\Model\Customer\ExtractCustomerData $subject
     * @param array $result
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array
     */
    public function afterExecute($subject, $result, $customer)
    {
        if ($customer->getCustomAttribute('profile_picture')) {
            $profile_picture = $this->urlBuilder->getUrl('customermyprofile/myprofile/profilepictureview/', ['image' => base64_encode($customer->getCustomAttribute('profile_picture')->getValue())]);
        } else {
            $profile_picture = $this->assetRepo->getUrlWithParams('Trans_CustomerMyProfile::images/no-profile-photo.png', ['area' => 'frontend']);
        }

        $result['profile_picture'] = $profile_picture;

        return $result;
    }
}
