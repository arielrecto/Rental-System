<?php

namespace App\Http\Controllers\Customer;

use Inertia\Inertia;
use App\Models\Payment;
use App\Models\RentalOrder;
use Illuminate\Http\Request;
use App\Models\PaymentAccount;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{


    public function history(Request $request)
    {
        $query = Payment::with(['paymentAccount', 'paidBy', 'payable'])
            ->where('paid_by', auth()->user()->id);


        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_account_id')) {
            $query->where('payment_account_id', $request->payment_account_id);
        }

        if ($request->has('date_range')) {
            $query->whereBetween('created_at', $request->date_range);
        }

        $payments = $query->latest()
            ->get()
            ->map(fn($payment) => $this->transformPayment($payment));

        $paymentAccounts = PaymentAccount::where('is_active', true)->get();

        return Inertia::render('Customer/Payment/PaymentHistory', [
            'payments' => $payments,
            'paymentAccounts' => $paymentAccounts,
            'statuses' => ['pending', 'completed', 'failed', 'refunded']
        ]);
    }

    public function create()
    {
        $paymentAccounts = PaymentAccount::where('is_active', true)->get();
        $payableItems = $this->getPayableItems();

        return Inertia::render('Customer/Payment/PaymentCreate', [
            'paymentAccounts' => $paymentAccounts,
            'payableItems' => $payableItems
        ]);
    }


    public function edit(string $id)
    {
        $payment = Payment::with(['paymentAccount', 'paidBy', 'payable'])
            ->findOrFail($id);
        $paymentAccounts = PaymentAccount::where('is_active', true)->get();
        $payableItems = $this->getPayableItems();

        return Inertia::render('Customer/Payment/PaymentEdit', [
            'payment' => $this->transformPayment($payment),
            'paymentAccounts' => $paymentAccounts,
            'payableItems' => $payableItems
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'payment_account_id' => 'required|exists:payment_accounts,id',
            'ref_number' => 'required|string|unique:payments,ref_number,' . $id,
            'total_amount' => 'required|numeric|min:0',
            'memo' => 'nullable|string',
            'status' => 'required|in:pending'
        ]);

        $payment->update($validated);

        return redirect()->route('internal.payments.index')
            ->with('success', 'Payment updated successfully');
    }

    private function getPayableItems()
    {
        $rentalOrders = RentalOrder::with('user')
            ->where('user_id', auth()->user()->id)
            ->whereDoesntHave('payments', function ($query) {
                $query->where('status', 'completed');
            })
            ->where('status', '!=', 'paid')
            ->get()
            ->map(fn($order) => [
                'id' => $order->id,
                'type' => get_class($order),
                'ref_number' => $order->ref_number,
                'amount' => $order->total_amount,
                'customer' => $order->user->name,
                'date' => $order->rental_date,
                'customer_id' => $order->user->id,
            ]);

        // Add more payable types here
        // $otherPayables = OtherModel::where(...)->get()->map(...);

        return [
            'rental_orders' => $rentalOrders,
            // 'other_payables' => $otherPayables,
        ];
    }


    private function transformPayment($payment)
    {
        return [
            'id' => $payment->id,
            'ref_number' => $payment->ref_number,
            'total_amount' => $payment->total_amount,
            'status' => $payment->status,
            'memo' => $payment->memo,
            'created_at' => $payment->created_at,
            'payment_account' => [
                'id' => $payment->paymentAccount->id,
                'name' => $payment->paymentAccount->account_name,
                'provider' => $payment->paymentAccount->provider,
            ],
            'paid_by' => [
                'id' => $payment->paidBy->id,
                'name' => $payment->paidBy->name,
            ],
            'payable' => $this->transformPayable($payment->payable),
        ];
    }


    private function transformPayable($payable)
    {
        if (!$payable) return null;

        $type = class_basename($payable);

        switch ($type) {
            case 'RentalOrder':
                return [
                    'id' => $payable->id,
                    'type' => 'Rental Order',
                    'ref_number' => $payable->ref_number,
                    'amount' => $payable->total_amount,
                    'customer' => $payable->user->name,
                    'date' => $payable->rental_date,
                ];
                // Add more cases for other payable types
            default:
                return [
                    'id' => $payable->id,
                    'type' => $type,
                    'ref_number' => $payable->ref_number ?? 'N/A',
                ];
        }
    }
}
