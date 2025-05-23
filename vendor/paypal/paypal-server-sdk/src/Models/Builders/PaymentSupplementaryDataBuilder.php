<?php

declare(strict_types=1);

/*
 * PaypalServerSdkLib
 *
 * This file was automatically generated by APIMATIC v3.0 ( https://www.apimatic.io ).
 */

namespace PaypalServerSdkLib\Models\Builders;

use Core\Utils\CoreHelper;
use PaypalServerSdkLib\Models\PaymentSupplementaryData;
use PaypalServerSdkLib\Models\RelatedIdentifiers;

/**
 * Builder for model PaymentSupplementaryData
 *
 * @see PaymentSupplementaryData
 */
class PaymentSupplementaryDataBuilder
{
    /**
     * @var PaymentSupplementaryData
     */
    private $instance;

    private function __construct(PaymentSupplementaryData $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Initializes a new Payment Supplementary Data Builder object.
     */
    public static function init(): self
    {
        return new self(new PaymentSupplementaryData());
    }

    /**
     * Sets related ids field.
     *
     * @param RelatedIdentifiers|null $value
     */
    public function relatedIds(?RelatedIdentifiers $value): self
    {
        $this->instance->setRelatedIds($value);
        return $this;
    }

    /**
     * Initializes a new Payment Supplementary Data object.
     */
    public function build(): PaymentSupplementaryData
    {
        return CoreHelper::clone($this->instance);
    }
}
