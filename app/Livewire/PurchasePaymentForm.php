<?php

namespace App\Livewire;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Services\PayPalService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PurchasePaymentForm extends Component
{
    public Purchase $purchase;

    public bool $showPaymentModal = false;

    public string $amount = '';

    public bool $processing = false;

    public function mount(Purchase $purchase): void
    {
        $this->purchase = $purchase->load([
            'supplier',
            'payments',
        ]);
    }

    public function openPaymentModal(): void
    {
        $this->resetValidation();

        $this->amount = '';

        if (!$this->purchase->supplier) {
            session()->flash(
                'error',
                'Supplier not found.'
            );

            return;
        }

        if (!$this->purchase->supplier->paypal_email) {
            session()->flash(
                'error',
                'Supplier does not have a PayPal email.'
            );

            return;
        }

        if ($this->remainingAmount <= 0) {
            session()->flash(
                'error',
                'This purchase is already fully paid.'
            );

            return;
        }

        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;

        $this->resetValidation();

        $this->amount = '';
    }

    public function getPaidAmountProperty(): float
    {
        return (float) $this->purchase
            ->payments()
            ->where(function ($query) {

                $query
                    ->whereNull('provider')

                    ->orWhere(function ($query) {

                        $query
                            ->where('provider', 'paypal')
                            ->where('provider_status', 'SUCCESS');

                    });

            })
            ->sum('amount');
    }

    public function getRemainingAmountProperty(): float
    {
        return max(
            (float) $this->purchase->total_price
            - $this->paidAmount,
            0
        );
    }

    public function getPaypalAmountProperty(): float
    {
        $amount = (float) $this->amount;

        if ($amount <= 0) {
            return 0;
        }

        $rate = (float) config(
            'services.paypal.egp_usd_rate'
        );

        if ($rate <= 0) {
            return 0;
        }

        return round(
            $amount / $rate,
            2
        );
    }

    public function payWithPaypal(
        PayPalService $paypal
    ): void {
        $this->resetValidation();

        $this->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ]);

        $amountEgp = (float) $this->amount;

        /*
        |--------------------------------------------------------------------------
        | Check remaining debt
        |--------------------------------------------------------------------------
        */

        if ($amountEgp > $this->remainingAmount) {

            $this->addError(
                'amount',
                'Payment amount cannot exceed remaining debt.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Supplier
        |--------------------------------------------------------------------------
        */

        $supplier = $this->purchase->supplier;

        if (!$supplier?->paypal_email) {

            session()->flash(
                'error',
                'Supplier does not have a PayPal email.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Exchange rate
        |--------------------------------------------------------------------------
        */

        $rate = (float) config(
            'services.paypal.egp_usd_rate'
        );

        if ($rate <= 0) {

            session()->flash(
                'error',
                'PayPal exchange rate is not configured.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | EGP -> USD
        |--------------------------------------------------------------------------
        */

        $paypalAmount = round(
            $amountEgp / $rate,
            2
        );

        if ($paypalAmount <= 0) {

            $this->addError(
                'amount',
                'Payment amount is too small.'
            );

            return;
        }

        $paypalCurrency = config(
            'services.paypal.payout_currency',
            'USD'
        );

        $this->processing = true;

        try {

            /*
            |--------------------------------------------------------------------------
            | IDs
            |--------------------------------------------------------------------------
            */

            $senderBatchId =
                'purchase-' .
                $this->purchase->id .
                '-' .
                uniqid();

            $senderItemId =
                'payment-' .
                $this->purchase->id .
                '-' .
                uniqid();

            /*
            |--------------------------------------------------------------------------
            | Send Payout
            |--------------------------------------------------------------------------
            */

            $paypalResponse = $paypal->createPayout(

                receiver:
                    $supplier->paypal_email,

                amount:
                    number_format(
                        $paypalAmount,
                        2,
                        '.',
                        ''
                    ),

                currency:
                    $paypalCurrency,

                senderBatchId:
                    $senderBatchId,

                senderItemId:
                    $senderItemId,

                note:
                    'Payment for Purchase #' .
                    $this->purchase->id
            );

            /*
            |--------------------------------------------------------------------------
            | Response
            |--------------------------------------------------------------------------
            */

            $batchHeader =
                $paypalResponse['batch_header'] ?? [];

            $payoutBatchId =
                $batchHeader['payout_batch_id'] ?? null;

            $batchStatus =
                $batchHeader['batch_status'] ?? null;

            if (!$payoutBatchId) {

                throw new \RuntimeException(
                    'PayPal did not return payout batch ID.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Payout Item
            |--------------------------------------------------------------------------
            */

            $payoutItem =
                $paypalResponse['items'][0] ?? [];

            $transactionId =
                $payoutItem['transaction_id'] ?? null;

            $payoutFee =
                $payoutItem['payout_item_fee']['value'] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Validate Status
            |--------------------------------------------------------------------------
            */

            if (!in_array(
                $batchStatus,
                [
                    'SUCCESS',
                    'PENDING',
                    'PROCESSING',
                ],
                true
            )) {

                throw new \RuntimeException(
                    'PayPal payout was not accepted. Status: ' .
                    $batchStatus
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Save Payment
            |--------------------------------------------------------------------------
            */

            $payment = DB::transaction(function () use (
                $amountEgp,
                $paypalAmount,
                $paypalCurrency,
                $rate,
                $payoutBatchId,
                $batchStatus,
                $transactionId,
                $payoutFee
            ) {

                return PurchasePayment::create([

                    'purchase_id' =>
                        $this->purchase->id,

                    'employee_id' =>
                        auth('employee')->id(),

                    /*
                    | ERP amount
                    */
                    'amount' =>
                        $amountEgp,

                    'currency' =>
                        'EGP',

                    /*
                    | PayPal amount
                    */
                    'paypal_amount' =>
                        $paypalAmount,

                    'paypal_currency' =>
                        $paypalCurrency,

                    'exchange_rate' =>
                        $rate,

                    'payment_method' =>
                        'paypal',

                    /*
                    | SUCCESS only
                    */
                    'paid_at' =>
                        $batchStatus === 'SUCCESS'
                            ? now()
                            : null,

                    'provider' =>
                        'paypal',

                    'provider_reference' =>
                        $payoutBatchId,

                    'paypal_transaction_id' =>
                        $transactionId,

                    'paypal_fee' =>
                        $payoutFee,

                    'provider_status' =>
                        $batchStatus,

                    'notes' =>
                        'Supplier payment via PayPal',
                ]);
            });

            /*
            |--------------------------------------------------------------------------
            | Reload
            |--------------------------------------------------------------------------
            */

            $this->purchase->load([
                'supplier',
                'payments',
            ]);

            $this->closePaymentModal();

            if ($batchStatus === 'SUCCESS') {

                session()->flash(
                    'message',
                    'Payment completed successfully.'
                );

            } else {

                session()->flash(
                    'message',
                    'Payment submitted to PayPal. Status: ' .
                    $batchStatus
                );
            }

        } catch (\Throwable $e) {

            report($e);

            session()->flash(
                'error',
                $e->getMessage()
            );

        } finally {

            $this->processing = false;
        }
    }

    public function checkPaymentStatus(
        int $paymentId,
        PayPalService $paypal
    ): void {

        $payment = PurchasePayment::query()
            ->where('id', $paymentId)
            ->where('purchase_id', $this->purchase->id)
            ->where('provider', 'paypal')
            ->firstOrFail();

        if (!$payment->provider_reference) {

            session()->flash(
                'error',
                'PayPal payout reference not found.'
            );

            return;
        }

        try {

            $response = $paypal->getPayout(
                $payment->provider_reference
            );

            $batchHeader =
                $response['batch_header'] ?? [];

            $status =
                $batchHeader['batch_status'] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Get item details
            |--------------------------------------------------------------------------
            */

            $payoutItem =
                $response['items'][0] ?? [];

            $transactionId =
                $payoutItem['transaction_id']
                ?? $payment->paypal_transaction_id;

            $payoutFee =
                $payoutItem['payout_item_fee']['value']
                ?? $payment->paypal_fee;

            /*
            |--------------------------------------------------------------------------
            | Update Payment
            |--------------------------------------------------------------------------
            */

            $payment->update([

                'provider_status' =>
                    $status,

                'paypal_transaction_id' =>
                    $transactionId,

                'paypal_fee' =>
                    $payoutFee,

                'paid_at' =>
                    $status === 'SUCCESS'
                        ? ($payment->paid_at ?? now())
                        : $payment->paid_at,
            ]);

            $this->purchase->load([
                'supplier',
                'payments',
            ]);

            session()->flash(
                'message',
                'PayPal status: ' . $status
            );

        } catch (\Throwable $e) {

            report($e);

            session()->flash(
                'error',
                'Unable to check PayPal status.'
            );
        }
    }

    public function render()
    {
        return view(
            'components.purchase-payment-form'
        );
    }
}