<?php

namespace SM\ShoppingList\Controller\Shared;

use LengthException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DB\Adapter\DuplicateException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Wishlist\Model\Wishlist;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Controller\ListAction;

/**
 * Class Index
 * @package SM\ShoppingList\Controller\Shared
 */
class Index extends ListAction
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->customerSession->setAfterAuthUrl(
                $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true])
            );
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        }

        /** @var Wishlist $shoppingList */
        $shoppingList = $this->wishlistProvider->getWishlist();
        if ($shoppingList->getId()) {
            try {
                /** @var ShoppingListDataInterface $result */
                $result = $this->shoppingListRepository
                    ->share($shoppingList->getId(), $this->currentCustomer->getCustomerId());
                $this->messageManager->addSuccessMessage(
                    __($result->getName() . " has been shared with you")
                );
            } catch (DuplicateException $e) {
                $this->messageManager->addErrorMessage(__("This list has already been in your shopping list"));
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__("Unable to find this list"));
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage(__("Something went wrong while saving this list"));
            } catch (LengthException $e) {
                $this->messageManager->addErrorMessage(__("You have reached maximum shopping list number"));
            }
        } else {
            $this->messageManager->addErrorMessage(__("Unable to find this list"));
        }

        return $this->_redirect($this->_url->getUrl("*/mylist"));
    }
}
