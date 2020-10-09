<?php


namespace SM\MobileApi\Api\Data\Promo;


/**
 * Interface PromoListingInterface
 * @package SM\MobileApi\Api\Data\Promo
 */
interface PromoListingInterface
{
    const ID     = 'id';
    const IMAGE_URL   = 'image';

    /**
     * @return string
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getImageUrl();

    /**
     * @param string $imageUrl
     * @return $this
     */
    public function setImageUrl($imageUrl);
}
