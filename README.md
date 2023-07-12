# Corals Aliexpress

AliExpress Module is an addon for Laraship eCommerce & Marketplace that gives you the ability to import products from AliExpress and sell them as affiliate products, you can filter products by keywords and categories with other importing options. the import will extract the following:

1.Product Attributes: title, description, price, AliExpress Affilate URL,

2.Product Images

3.Brand

4.Categories

5.Tags

AliExpress Module Manager is using Aliexpress Affiliates API o connect to Aliexpress to pull the products from.

<p>&nbsp;</p>

### How To Get Your Access Key ID & Secret Key
Getting your Access Key ID and Secret Key pair is easy enough, but it involves a number of steps.

1) It takes just a few minutes to get yourself an <strong>affiliate</strong> account on <strong>AliExpress</strong> and start selling products to earn a commission. In order to sign up for <strong>AliExpress affiliate</strong>, head over to the <strong>AliExpress</strong> website, scroll down to the bottom and click “<strong>Affiliate</strong> Program” then click Register and create an account

2) Once your account is approved, go to https://console.aliexpress.com/ and create a new app and fill in the required details


<p><img src="https://www.laraship.com/wp-content/uploads/2020/11/aliexpress_application_management.png" alt=""></p>


3) Click on the Management button to grab the API key and Secret.


<p><img src="https://www.laraship.com/wp-content/uploads/2020/11/aliexpress_app_settings.png" alt=""></p>


4) fill the details into AliExpress Settings Screen inside


<p><img src="https://www.laraship.com/wp-content/uploads/2020/11/aliexpress_app_settings-1024x392.png" alt=""></p> 


5) Insert the keys into Laraship module settings


<p><img src="https://www.laraship.com/wp-content/uploads/2020/11/laravel_aliexpress_settings.png" alt=""></p>


6) Create import details by filling keywords, categories, and results to be pulled


<p><img src="https://www.laraship.com/wp-content/uploads/2020/11/aliexpress_import_settings.png" alt=""></p>

<p>&nbsp;</p>

### Setup Your Cron Job:
AliExpress Importer uses background processes extract imports to avoid any timeout memory issues, jobs will be queued and the importer process will import only one job at a time, to set up your importer scheduler you need to add the following command to your crontab

```php
php artisan  import:alix
```
<p>&nbsp;</p>


## Installation

You can install the package via composer:

```bash
composer require corals/aliexpress
```

## Testing

```bash
vendor/bin/phpunit vendor/corals/aliexpress/tests 
```
