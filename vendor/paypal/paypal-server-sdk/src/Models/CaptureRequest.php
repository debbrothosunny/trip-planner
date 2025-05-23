<?php

declare(strict_types=1);

/*
 * PaypalServerSdkLib
 *
 * This file was automatically generated by APIMATIC v3.0 ( https://www.apimatic.io ).
 */

namespace PaypalServerSdkLib\Models;

use PaypalServerSdkLib\ApiHelper;
use stdClass;

class CaptureRequest implements \JsonSerializable
{
    /**
     * @var string|null
     */
    private $invoiceId;

    /**
     * @var string|null
     */
    private $noteToPayer;

    /**
     * @var Money|null
     */
    private $amount;

    /**
     * @var bool|null
     */
    private $finalCapture = false;

    /**
     * @var CapturePaymentInstruction|null
     */
    private $paymentInstruction;

    /**
     * @var string|null
     */
    private $softDescriptor;

    /**
     * Returns Invoice Id.
     * The API caller-provided external invoice number for this order. Appears in both the payer's
     * transaction history and the emails that the payer receives.
     */
    public function getInvoiceId(): ?string
    {
        return $this->invoiceId;
    }

    /**
     * Sets Invoice Id.
     * The API caller-provided external invoice number for this order. Appears in both the payer's
     * transaction history and the emails that the payer receives.
     *
     * @maps invoice_id
     */
    public function setInvoiceId(?string $invoiceId): void
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * Returns Note to Payer.
     * An informational note about this settlement. Appears in both the payer's transaction history and the
     * emails that the payer receives.
     */
    public function getNoteToPayer(): ?string
    {
        return $this->noteToPayer;
    }

    /**
     * Sets Note to Payer.
     * An informational note about this settlement. Appears in both the payer's transaction history and the
     * emails that the payer receives.
     *
     * @maps note_to_payer
     */
    public function setNoteToPayer(?string $noteToPayer): void
    {
        $this->noteToPayer = $noteToPayer;
    }

    /**
     * Returns Amount.
     * The currency and amount for a financial transaction, such as a balance or payment due.
     */
    public function getAmount(): ?Money
    {
        return $this->amount;
    }

    /**
     * Sets Amount.
     * The currency and amount for a financial transaction, such as a balance or payment due.
     *
     * @maps amount
     */
    public function setAmount(?Money $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * Returns Final Capture.
     * Indicates whether you can make additional captures against the authorized payment. Set to `true` if
     * you do not intend to capture additional payments against the authorization. Set to `false` if you
     * intend to capture additional payments against the authorization.
     */
    public function getFinalCapture(): ?bool
    {
        return $this->finalCapture;
    }

    /**
     * Sets Final Capture.
     * Indicates whether you can make additional captures against the authorized payment. Set to `true` if
     * you do not intend to capture additional payments against the authorization. Set to `false` if you
     * intend to capture additional payments against the authorization.
     *
     * @maps final_capture
     */
    public function setFinalCapture(?bool $finalCapture): void
    {
        $this->finalCapture = $finalCapture;
    }

    /**
     * Returns Payment Instruction.
     * Any additional payment instructions to be consider during payment processing. This processing
     * instruction is applicable for Capturing an order or Authorizing an Order.
     */
    public function getPaymentInstruction(): ?CapturePaymentInstruction
    {
        return $this->paymentInstruction;
    }

    /**
     * Sets Payment Instruction.
     * Any additional payment instructions to be consider during payment processing. This processing
     * instruction is applicable for Capturing an order or Authorizing an Order.
     *
     * @maps payment_instruction
     */
    public function setPaymentInstruction(?CapturePaymentInstruction $paymentInstruction): void
    {
        $this->paymentInstruction = $paymentInstruction;
    }

    /**
     * Returns Soft Descriptor.
     * The payment descriptor on the payer's account statement.
     */
    public function getSoftDescriptor(): ?string
    {
        return $this->softDescriptor;
    }

    /**
     * Sets Soft Descriptor.
     * The payment descriptor on the payer's account statement.
     *
     * @maps soft_descriptor
     */
    public function setSoftDescriptor(?string $softDescriptor): void
    {
        $this->softDescriptor = $softDescriptor;
    }

    /**
     * Converts the CaptureRequest object to a human-readable string representation.
     *
     * @return string The string representation of the CaptureRequest object.
     */
    public function __toString(): string
    {
        return ApiHelper::stringify(
            'CaptureRequest',
            [
                'invoiceId' => $this->invoiceId,
                'noteToPayer' => $this->noteToPayer,
                'amount' => $this->amount,
                'finalCapture' => $this->finalCapture,
                'paymentInstruction' => $this->paymentInstruction,
                'softDescriptor' => $this->softDescriptor
            ]
        );
    }

    /**
     * Encode this object to JSON
     *
     * @param bool $asArrayWhenEmpty Whether to serialize this model as an array whenever no fields
     *        are set. (default: false)
     *
     * @return array|stdClass
     */
    #[\ReturnTypeWillChange] // @phan-suppress-current-line PhanUndeclaredClassAttribute for (php < 8.1)
    public function jsonSerialize(bool $asArrayWhenEmpty = false)
    {
        $json = [];
        if (isset($this->invoiceId)) {
            $json['invoice_id']          = $this->invoiceId;
        }
        if (isset($this->noteToPayer)) {
            $json['note_to_payer']       = $this->noteToPayer;
        }
        if (isset($this->amount)) {
            $json['amount']              = $this->amount;
        }
        if (isset($this->finalCapture)) {
            $json['final_capture']       = $this->finalCapture;
        }
        if (isset($this->paymentInstruction)) {
            $json['payment_instruction'] = $this->paymentInstruction;
        }
        if (isset($this->softDescriptor)) {
            $json['soft_descriptor']     = $this->softDescriptor;
        }

        return (!$asArrayWhenEmpty && empty($json)) ? new stdClass() : $json;
    }
}
