<?php

declare(strict_types=1);

/*
 * PaypalServerSdkLib
 *
 * This file was automatically generated by APIMATIC v3.0 ( https://www.apimatic.io ).
 */

namespace PaypalServerSdkLib\Models;

/**
 * The location from which the shipping address is derived.
 */
class PaypalWalletContextShippingPreference
{
    /**
     * Get the customer-provided shipping address on the PayPal site.
     */
    public const GET_FROM_FILE = 'GET_FROM_FILE';

    /**
     * Removes the shipping address information from the API response and the Paypal site. However, the
     * shipping.phone_number and shipping.email_address fields will still be returned to allow for digital
     * goods delivery.
     */
    public const NO_SHIPPING = 'NO_SHIPPING';

    /**
     * Get the merchant-provided address. The customer cannot change this address on the PayPal site. If
     * merchant does not pass an address, customer can choose the address on PayPal pages.
     */
    public const SET_PROVIDED_ADDRESS = 'SET_PROVIDED_ADDRESS';
}
