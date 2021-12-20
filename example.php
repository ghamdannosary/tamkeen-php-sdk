<?php

use PCsoft\Tamkeen\Resources\Currency;
use PCsoft\Tamkeen\Tamkeen;

$tamkeen = new Tamkeen();
$tamkeen->setKey('YOU_MERCHANT_KEY');
$tamkeen->setCvvKey('YOUR_CVV_KEY');
$tamkeen->setUsername('YOUR_MERCHANT_USERNAME');
$tamkeen->setPassword('YOUR_MERCHANT_PASSWORD');
$tamkeen->setServiceId('YOUR_SERVICE_ID');
$tamkeen->setCertificate('YOUR_CERTIFICATE_PATH', 'YOUR_CERTIFICATE_PASSWORD');
$tamkeen->build('YOUR_PORT');

$tamkeen->createPayment(
    phone:'773769681',
    amount:100,
    currency:Currency::YER,
);
