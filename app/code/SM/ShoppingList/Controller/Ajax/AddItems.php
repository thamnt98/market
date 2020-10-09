<?php

namespace SM\ShoppingList\Controller\Ajax;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\MultipleWishlist\Helper\Data;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Controller\ItemAction;
use SM\ShoppingList\Helper\Data as ShoppingListHelper;
use SM\ShoppingList\Model\ShoppingListItemRepository;

/**
 * Class AddItems
 * @package SM\ShoppingList\Controller\Ajax
 */
class AddItems extends ItemAction
{
    protected $escaper;

    public function __construct(
        \Magento\Framework\Escaper $escaper,
        Data $wishlistData,
        ShoppingListItemRepository $shoppingListItemRepository,
        JsonFactory $jsonFactory,
        Context $context,
        CurrentCustomer $currentCustomer,
        ShoppingListHelper $shoppinglistHelper
    ) {
        $this->escaper = $escaper;
        parent::__construct(
            $wishlistData,
            $shoppingListItemRepository,
            $jsonFactory,
            $context,
            $currentCustomer,
            $shoppinglistHelper
        );
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $productId = $data["product_id"];

        if (isset($data["shopping_list_ids"])) {
            $shoppingListIds = $data["shopping_list_ids"];
        } else {
            $shoppingListIds = [$this->wishlistData->getDefaultWishlist()->getId()];
        }
        $storeId = $data["store_id"];
        $myFavoriteListId = $this->wishlistData->getDefaultWishlist()->getId();

        $result = $this->shoppingListItemRepository->add($shoppingListIds, $productId, $storeId);
        $isShowToast = $this->isShowToast($data);
        if ($result->getStatus()) {
            $data = [];
            /** @var ShoppingListDataInterface $list */
            foreach ($result->getResult() as $list) {
                $data[] = [
                    "name" => $list->getName(),
                    "url" => $this->_url->getUrl(
                        "shoppinglist/mylist/detail",
                        ["id" => $list->getWishlistId()]
                    ),
                ];
            }
            if ($isShowToast) {
                if (sizeof($shoppingListIds) > 1) {
                    $url =  $this->_url->getUrl("shoppinglist/mylist/detail", ["id" => $myFavoriteListId]);
                    $this->messageManager->addComplexSuccessMessage(
                        'addShoppingListMessageSuccessItems',
                        [
                            'url' => $url
                        ]
                    );
                } else {
                    $name = $this->escaper->escapeHtml($list->getName());
                    $url =  $this->_url->getUrl("shoppinglist/mylist/detail", ["id" => $list->getWishlistId()]);
                    $this->messageManager->addComplexSuccessMessage(
                        'addShoppingListMessageSuccessItem',
                        [
                            'name' => $name,
                            'url' => $url
                        ]
                    );
                }
            }

            return $this->jsonFactory->create()->setData([
                "myFavoriteListId" => $myFavoriteListId,
                "status" => $result->getStatus(),
                "result" => $data
            ]);
        } else {
            return $this->jsonFactory->create()->setData([
                "myFavoriteListId" => $myFavoriteListId,
                "status" => $result->getStatus(),
                "result" => $result->getMessage()
            ]);
        }
    }

    public function isShowToast($data)
    {
        return isset($data["show_toast"]) && $data["show_toast"] == "true";
    }
}
