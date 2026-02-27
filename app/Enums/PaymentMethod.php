<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case CreditCard = 'credit_card';

    case BankTransfer = 'bank_transfer';

    case Paypal = 'paypal';

    case ApplePay = 'apple_pay';

    case GooglePay = 'google_pay';

    public function getLabel(): string
    {
        return match ($this) {
            self::CreditCard => __('Credit Card'),
            self::BankTransfer => __('Bank Transfer'),
            self::Paypal => __('PayPal'),
            self::ApplePay => __('Apple Pay'),
            self::GooglePay => __('Google Pay'),
        };
    }
}
