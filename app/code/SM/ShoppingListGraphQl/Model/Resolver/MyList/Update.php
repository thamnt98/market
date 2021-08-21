<?php

namespace SM\ShoppingListGraphQl\Model\Resolver\MyList;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Webapi\Exception as WebapiException;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListDataInterfaceFactory;
use SM\ShoppingList\Model\ShoppingListItemRepository;
use SM\ShoppingList\Model\ShoppingListRepository;

/**
 * Class Update
 * @package SM\ShoppingListGraphQl\Model\Resolver\MyList
 */
class Update implements ResolverInterface
{
    /**
     * @var ShoppingListDataInterfaceFactory
     */
    protected $listDataFactory;

    /**
     * @var ShoppingListRepository
     */
    protected $shoppingListRepository;

    /**
     * Create constructor.
     * @param ShoppingListItemRepository $shoppingListItemRepository
     * @param ShoppingListDataInterfaceFactory $listDataFactory
     */
    public function __construct(
        ShoppingListRepository $shoppingListRepository,
        ShoppingListDataInterfaceFactory $listDataFactory
    )
    {
        $this->shoppingListRepository = $shoppingListRepository;
        $this->listDataFactory = $listDataFactory;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();
        /* Guest checking */
        if (!$customerId && 0 === $customerId) {
            throw new GraphQlAuthorizationException(__('The current user cannot perform operations on wishlist'));
        }
        if (!isset($args['update_list_name'])) {
            throw new GraphQlInputException(__('"update list name" value should be specified'));
        }
        if (!isset($args['my_list_id'])) {
            throw new GraphQlInputException(__('"my list id" value should be specified'));
        }

        $updateListName = $args['update_list_name'];
        $myListId = $args['my_list_id'];

        /** @var ShoppingListDataInterface $listData */
        $listData = $this->listDataFactory->create();
        $listData->setName($updateListName);
        $listData->setWishlistId($myListId);

        try {
            /** @var ShoppingListDataInterface $result */
            $result = $this->shoppingListRepository->update(
                $listData,
                $customerId
            );
            return [
                "status" => 1,
                "message" => __("You have successfully renamed this list."),
                "result" => [
                    "name" => $result->getName(),
                    "my_list_id" => $result->getWishlistId()
                ]
            ];
        } catch (WebapiException $e) {
            return [
                "status" => 0,
                "message" => $e->getMessage(),
                "result" => []
            ];
        }
    }
}
