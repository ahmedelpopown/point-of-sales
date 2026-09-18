<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Services\PayPalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PurchasePaymentController extends Controller
{
    public function payWithPaypal(
        Request $request,
        Purchase $purchase,
        PayPalService $paypal
    ): JsonResponse {
        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ]);

        $purchase->load('supplier');

        if (!$purchase->supplier) {
            return response()->json([
                'message' => 'Purchase supplier not found.',
            ], 422);
        }

        if (!$purchase->supplier->paypal_email) {
            return response()->json([
                'message' => 'Supplier does not have a PayPal email.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Paid Amount
        |--------------------------------------------------------------------------
        |
        | Only SUCCESS PayPal payments are considered paid.
        |
        */
        $paidAmount = $purchase->payments()
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

        $remainingAmount = max(
            (float) $purchase->total_price - (float) $paidAmount,
            0
        );

        $amount = (float) $validated['amount'];

        if ($remainingAmount <= 0) {
            return response()->json([
                'message' => 'This purchase is already fully paid.',
            ], 422);
        }

        if ($amount > $remainingAmount) {
            return response()->json([
                'message' => 'Payment amount exceeds remaining debt.',
                'remaining_amount' => $remainingAmount,
            ], 422);
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | PayPal
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | `amount` in your ERP is EGP.
            | The amount sent to PayPal must be in a supported PayPal currency.
            |
            | This is currently kept as-is for Sandbox testing only.
            | Replace this with your real currency conversion before production.
            |
            */

            $paypalAmount = $amount;

            $paypalCurrency = config(
                'services.paypal.payout_currency',
                'USD'
            );

            /*
            |--------------------------------------------------------------------------
            | Unique PayPal IDs
            |--------------------------------------------------------------------------
            */

            $senderBatchId =
                'purchase-' .
                $purchase->id .
                '-' .
                uniqid();

            $senderItemId =
                'purchase-payment-' .
                $purchase->id .
                '-' .
                uniqid();

            /*
            |--------------------------------------------------------------------------
            | Create PayPal Payout
            |--------------------------------------------------------------------------
            */

            $paypalResponse = $paypal->createPayout(
                receiver: $purchase->supplier->paypal_email,
                amount: number_format(
                    $paypalAmount,
                    2,
                    '.',
                    ''
                ),
                currency: $paypalCurrency,
                senderBatchId: $senderBatchId,
                senderItemId: $senderItemId,
                note: 'Payment for Purchase #' . $purchase->id,
            );

            $batchHeader =
                $paypalResponse['batch_header'] ?? [];

            $payoutBatchId =
                $batchHeader['payout_batch_id'] ?? null;

            $batchStatus =
                $batchHeader['batch_status'] ?? null;

            if (!$payoutBatchId) {
                return response()->json([
                    'message' => 'PayPal did not return payout batch ID.',
                    'paypal_response' => $paypalResponse,
                ], 502);
            }

            /*
            |--------------------------------------------------------------------------
            | Validate PayPal Status
            |--------------------------------------------------------------------------
            */

            if (!in_array(
                $batchStatus,
                ['SUCCESS', 'PENDING', 'PROCESSING'],
                true
            )) {
                return response()->json([
                    'message' => 'PayPal payout was not accepted.',
                    'status' => $batchStatus,
                    'paypal_response' => $paypalResponse,
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Create Payment Record
            |--------------------------------------------------------------------------
            */

            $payment = DB::transaction(function () use (
                $purchase,
                $amount,
                $paypalAmount,
                $paypalCurrency,
                $payoutBatchId,
                $batchStatus
            ) {
                return PurchasePayment::create([
                    'purchase_id' => $purchase->id,

                    /*
                    | Use employee guard if that is your authenticated guard.
                    */
                    'employee_id' => auth('employee')->id(),

                    /*
                    | Your ERP amount
                    */
                    'amount' => $amount,
                    'currency' => 'EGP',

                    /*
                    | PayPal actual amount
                    */
                    'paypal_amount' => $paypalAmount,
                    'paypal_currency' => $paypalCurrency,

                    'exchange_rate' => null,

                    'payment_method' => 'paypal',

                    /*
                    | Do not mark paid until PayPal becomes SUCCESS
                    */
                    'paid_at' => $batchStatus === 'SUCCESS'
                        ? now()
                        : null,

                    'provider' => 'paypal',

                    'provider_reference' => $payoutBatchId,

                    'provider_status' => $batchStatus,

                    'notes' => 'Supplier payment via PayPal',
                ]);
            });

            return response()->json([
                'message' => 'PayPal payout created successfully.',
                'payment' => $payment,
                'paypal_batch_id' => $payoutBatchId,
                'paypal_status' => $batchStatus,
                'amount_egp' => $amount,
                'amount_paypal' => $paypalAmount,
                'paypal_currency' => $paypalCurrency,
                'remaining_before_payment' => $remainingAmount,
                'remaining_after_payment' => max(
                    $remainingAmount -
                    ($batchStatus === 'SUCCESS' ? $amount : 0),
                    0
                ),
            ], 201);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'message' => 'Unable to process PayPal payment.',
            ], 500);
        }
    }

    public function status(
        Purchase $purchase,
        PurchasePayment $payment,
        PayPalService $paypal
    ): JsonResponse {
        if (
            $payment->purchase_id !== $purchase->id ||
            $payment->provider !== 'paypal'
        ) {
            return response()->json([
                'message' => 'Invalid payment.',
            ], 404);
        }

        if (!$payment->provider_reference) {
            return response()->json([
                'message' => 'PayPal reference not found.',
            ], 422);
        }

        try {

            $paypalResponse = $paypal->getPayout(
                $payment->provider_reference
            );

            $batchStatus =
                $paypalResponse['batch_header']['batch_status']
                ?? null;

            $payment->update([
                'provider_status' => $batchStatus,

                'paid_at' => $batchStatus === 'SUCCESS'
                    ? ($payment->paid_at ?? now())
                    : $payment->paid_at,
            ]);

            return response()->json([
                'message' => 'Payment status updated.',
                'payment' => $payment->fresh(),
                'paypal_status' => $batchStatus,
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'message' => 'Unable to retrieve PayPal payment status.',
            ], 500);
        }
    }
}