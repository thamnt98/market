<?php
/**
 * @category Trans
 * @package  Trans_Core
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Core\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Email
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->transportBuilder = $transportBuilder;
        
        parent::__construct($context);
    }

    /**
     * Send email notification
     * 
     * @param string $emailTo
     * @param array $emptyProduct
     * @return void
     */
    public function sendEmail($emailTo, $var, $emailTemplate, $senderName = null, $senderEmail = null)
    {
        try {
            $sender['name'] = $senderName != null ? $senderName : $this->scopeConfig->getValue('trans_email/ident_general/name',ScopeInterface::SCOPE_STORE);
            $sender['email'] = $senderEmail != null ? $senderEmail : $this->scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);
            
            $transport = $this->transportBuilder
            ->setTemplateIdentifier($emailTemplate) // this code we have mentioned in the email_templates.xml
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars($var)
            ->setFrom($sender)
            ->addTo($emailTo)
            ->getTransport();
             
            $transport->sendMessage();
            
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        
        return;
    }
}