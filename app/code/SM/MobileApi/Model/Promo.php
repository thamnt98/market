<?php

namespace SM\MobileApi\Model;

use SM\MobileApi\Api\PromoInterface;
use SM\MobileApi\Model\Data\Promo\PromoListingFactory;

/**
 * Class Promo
 * @package SM\MobileApi\Model
 */
class Promo implements PromoInterface
{
    protected $promoListingFactory;

    public function __construct(PromoListingFactory $promoListingFactory)
    {
        $this->promoListingFactory = $promoListingFactory;
    }

    /**
     * @return array|\SM\MobileApi\Api\Data\Promo\PromoListingInterface[]
     */
    public function getPromoListing()
    {
        $data = [];

        foreach ($this->_getDummyData() as $promo) {
            $promoListing = $this->promoListingFactory->create();
            $promoListing->setId($promo['id']);
            $promoListing->setImageUrl($promo['imageUrl']);
            $data[] = $promoListing;
        }
        return $data;
    }

    protected function _getDummyData()
    {
        return [
            [
                'id' => 1,
                'imageUrl' => 'https://chemicalsinourlife.echa.europa.eu/documents/23718410/23807413/c_cosmetics_lg.jpg/e22a4675-5c9e-8e05-4649-7042a9aed269?t=1560171643352'
            ],
            [
                'id' => 2,
                'imageUrl' => 'https://miro.medium.com/max/800/0*a8__BlPyJgnS0bXa.jpg'
            ],
            [
                'id' => 3,
                'imageUrl' => 'https://img.theculturetrip.com/x/smart/wp-content/uploads/2018/03/cosmetics.jpg'
            ],
            [
                'id' => 4,
                'imageUrl' => 'https://heavyeditorial.files.wordpress.com/2019/03/best-shampoos-for-damaged-hair.jpg?quality=65&strip=all'
            ],
            [
                'id' => 5,
                'imageUrl' => 'https://www.apgroup.com/sg/en/resource/images/our-values/heritage-ingredients/green-tea/content/image_10.jpg'
            ],
            [
                'id' => 6,
                'imageUrl' => 'https://freedesignfile.com/upload/2018/02/Green-tea-cosmetic-adv-poster-design-vector-02.jpg'
            ],
        ];
    }
}
