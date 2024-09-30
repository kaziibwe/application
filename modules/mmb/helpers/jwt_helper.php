<?php

defined('BASEPATH') || exit('No direct script access allowed');

use Firebase\JWT\JWT as Customer_Rest_JWT;
use Firebase\JWT\Key as Customer_Rest_Key;

/**
 * [Decode JWT token and get original data].
 *
 * @param string $encodedToken   [token]
 * @param string $jwt_secret_key [purchase_code]
 *
 * @return array [decoded information]
 */
function DecodeJWTtoken(string $encodedToken, string $jwt_secret_key)
{
    // new logic added
    try {
        $decodedToken = Customer_Rest_JWT::decode($encodedToken, new Customer_Rest_Key($jwt_secret_key, 'HS512'));

        return $decodedToken;
    } catch (Exception $e) {
        // echo $e->getMessage();
        return false;
    }
}

/**
 * [generate JWT token from puchase code and buyer's information].
 *
 * @param array  $data           [payload data]
 * @param string $jwt_secret_key [purchase_code]
 *
 * @return string [token]
 */
function EncodeJWTtoken(array $data, string $jwt_secret_key)
{
    // 1 week = 604800 seconds
    $payload = [
                'purchase_code'    => $data['purchase_code'],
                'item_id'          => $data['item_id'],
                'buyer'            => $data['buyer'],
                'purchase_count'   => $data['purchase_count'],
                'activated_domain' => $data['activated_domain'],
                'ip'               => $data['ip'],
                'purchase_time'    => $data['purchase_time'],
                'check_interval'   => 604800,
            ];

    $encodedToken = Customer_Rest_JWT::encode($payload, $jwt_secret_key, 'HS512');

    return $encodedToken;
}
