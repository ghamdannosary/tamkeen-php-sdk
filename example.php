<?php

use PCsoft\Tamkeen\Resources\Currency;
use PCsoft\Tamkeen\Resources\OperationStatusType;
use PCsoft\Tamkeen\Tamkeen;

$tamkeen = new Tamkeen();
$tamkeen->setUsername('YOUR_USERNAME');
$tamkeen->setPassword('YOUR_PASSWORD');
$tamkeen->setServiceProviderId('YOUR_SERVICE_PROVIDER_ID');
$tamkeen->setEncryptionKey('YOUR_ENCRYPTION_KEY');
$tamkeen->setCertificate('YOUR_CERTIFICATE_PATH', 'YOUR_CERTIFICATE_PASSWORD');
$tamkeen->build('YOUR_PORT');

$payment = $tamkeen->createPayment(
    phone:'773769681',
    cvvKey:123,
    amount:100,
    currency:Currency::YER,
);

// Confirm payment
$tamkeen->confirmPayment(
    ref:$payment->TransactionRef,
    otp:123456, //YOUR_OTP_HERE
);

// Confirm payment
$tamkeen->checkPayment(
    ref:$payment->TransactionRef,
    type:OperationStatusType::CONFIRMED,
);
