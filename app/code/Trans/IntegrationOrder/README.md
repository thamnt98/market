### Integration Order ###

# How to Get Order Allocation Rule Response from OMS (Order Management System) :

# The following is the function used to get Order Allocation Rule : 
1. app\code\Trans\IntegrationOrder\Model\AllocationRuleIntegration.php
2. app\code\Trans\IntegrationOrder\Model\IntegrationOrderAllocationRule.php
3. app\code\Trans\IntegrationOrder\Model\IntegrationOrderAllocationRuleRepository.php
4. app\code\Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderAllocationRule.php
5. app\code\Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderAllocationRule\Collection.php
6. app\code\Trans\IntegrationOrder\Observer\AllocationRuleOms.php
7. app\code\Trans\IntegrationOrder\Api\OrderAllocationRuleInterface.php
8. app\code\Trans\IntegrationOrder\Api\IntegrationOrderAllocationRuleRepositoryInterface.php
9. app\code\Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface.php

# app\code\Trans\IntegrationOrder\Model\AllocationRuleIntegration.php

**public function prepareOrderAllocation()** this function is to prepare the parameter request data to be sent to the OMS system.

**public function getOrderAllocationRule()** this function is in charge of sending data requests to the Order Management System and will get a response in accordance with the data sent. If the data sent is appropriate (within the OMS) then the output results received will appear.


### General Configuration
- different from the Order Creation for the Order Allocation Rule has a different URL. In the admin view there is the Get OAR API.

Go to `Admin Panel > Stores > Configuration > Trans > Order Management System`

### How To Use
1. Make sure the customer address is filled in, `such as address, zip code, latitude, longitude, district, city, province`. 
   e.g. : Latitude and Longitude can auto-fill if pinpoint activated, if not you can fill manual on Admin Magento.
2. Make sure the product you buy matches the store that is the one location with your address.
   e.g  : your address location at `city Jakarta Barat`, `district Kembangan`, and `zipcode 11610`. Product must be in PIM & IMS
3. Go to checkout, choose your address, choose your shipping method and click next. After at payment page refresh it and 
   you will see data above the header.
   note : using number 3 method if you change your address to get a new Store code. 

