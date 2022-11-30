<?php

namespace App\Utils;

use Braintree\Gateway;

class BraintreeUtil {
    static public function gateway(): Gateway {
        $config = config('services.braintree');
        return new Gateway([
            'environment' => $config['environment'] === 'production' ? 'production' : 'sandbox',
            'merchantId' => $config['merchant_id'],
            'publicKey' => $config['public_key'],
            'privateKey' => $config['private_key'],
        ]);
    }
}