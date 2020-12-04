<?php


namespace SM\MobileApi\Api;


/**
 * Interface PromoInterface
 * @package SM\MobileApi\Api
 */
interface PromoInterface
{
    /**
     * @return \SM\MobileApi\Api\Data\Promo\PromoListingInterface[]
     * @deprecated
     */
    public function getPromoListing();
}
