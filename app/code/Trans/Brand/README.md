##Shop By Brand Extension
Shop By Brand extension allows creating attributes, brand pages, brand logo sliders effectively using blocks, templates, layouts and widgets thereby gain the user trust and credibility.

##Support: 
version - 2.3.x

##How to install Extension

1. Download the archive file.
2. Unzip the files
3. Create a folder [Magento_Root]/app/code/Trans/Brand
4. Drop/move the unzipped files

#Enable Extension:
- php bin/magento module:enable Trans_Brand
- php bin/magento setup:upgrade
- php bin/magento cache:clean
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush

#Disable Extension:
- php bin/magento module:disable Trans_Brand
- php bin/magento setup:upgrade
- php bin/magento cache:clean
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush
