# Sprint Payment

## Overview

<!-- MarkdownTOC autolink="true" bracket="round" markdown_preview="markdown" -->

- [Installation](#installation)
    - [From Repository](#from-repository)
    - [Manual Installation](#manual-installation)
- [Requiered Module](#requiered-module)
- [How to Use](#how-to-use)

<!-- /MarkdownTOC -->

## Installation

### From Repository

1. Pull from repository
2. Just run `php bin/magento setup:upgrade` and `php bin/magento setup:di:compile` in Terminal

### Manual Installation

1. Copy and Paste Folder `Sprint` to `.../app/code/Trans/`
2. Just run `php bin/magento setup:upgrade` and `php bin/magento setup:di:compile` in Terminal

## Requiered Module

After install this module, you can also check `IntegrationOrder` module was installed or not. This module dependencies to `IntegrationOrder` in file `Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface`.

## How to Use

After done Installation and check depedencies you must setting this module in admin for run perfectly.

### Payment Group

1. Setting Payment Group in `Store > Configuration > Trans > Sprint Setting > Custom Payment Group Label`
2. You can set payment group label, enable/disable, and delete or add new

### Installement Terms Set

1. Setting Installment Terms in `Store > Configuration > Trans > Sprint Setting > Installment Term Set`
2. You can add or delete payment terms

### Payment Setting

1. You can setting payment credentials in `Store > Confifiguration > Sales > Payment Methods > Sprint Payment`
2. For credential you can ask to PO
