<?php

declare(strict_types=1);

/*
 * PaypalServerSdkLib
 *
 * This file was automatically generated by APIMATIC v3.0 ( https://www.apimatic.io ).
 */

namespace PaypalServerSdkLib\Models;

/**
 * A classification for the method of purchase fulfillment.
 */
class ShippingType
{
    /**
     * The payer intends to receive the items at a specified address.
     */
    public const SHIPPING = 'SHIPPING';

    /**
     * DEPRECATED. To ensure that seller protection is correctly assigned, please use 'PICKUP_IN_STORE' or
     * 'PICKUP_FROM_PERSON' instead. Currently, this field indicates that the payer intends to pick up the
     * items at a specified address (ie. a store address).
     */
    public const PICKUP = 'PICKUP';

    /**
     * The payer intends to pick up the item(s) from the payee's physical store. Also termed as BOPIS, "Buy
     * Online, Pick-up in Store". Seller protection is provided with this option.
     */
    public const PICKUP_IN_STORE = 'PICKUP_IN_STORE';

    /**
     * The payer intends to pick up the item(s) from the payee in person. Also termed as BOPIP, "Buy Online,
     * Pick-up in Person". Seller protection is not available, since the payer is receiving the item from
     * the payee in person, and can validate the item prior to payment.
     */
    public const PICKUP_FROM_PERSON = 'PICKUP_FROM_PERSON';
}
