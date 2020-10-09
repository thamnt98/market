<?php

/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Ilma Dinnia Alghani <ilma.dinnia@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Controller\Index;

use Trans\IntegrationCatalog\Api\ProductAssociationLogicInterface;

class Index extends \Magento\Framework\App\Action\Action
{

     /**
     * @var ProductAssociationInterface
     */
    protected $productAssociationInterface;
    /**
     * @param Context $context
     * @param ProductAssociationInterface $productAssociationInterface
     */
   
    public function __construct(\Magento\Framework\App\Action\Context $context, 
    ProductAssociationLogicInterface  $productAssociationInterface )
    {
        parent::__construct($context);
        $this->_productAssociation =  $productAssociationInterface;
   
    }
    public function execute()
    {    
        
        $data = $this->_productAssociation->saveProductAssociation();
        return true;
    }
}





