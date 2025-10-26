<?php

namespace App\Http\Controllers\Internal;

use Inertia\Inertia;

use App\Actions\GenerateSequence;
use App\Actions\UpdateStatusModelPayable;
use App\Models\Payment;
use App\Models\RentalOrder;
use Illuminate\Http\Request;
use App\Models\PaymentAccount;
use App\Http\Controllers\Controller;


class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        $payments = Payment::with(['paymentAccount', 'paidBy', 'payable'])
            ->latest()
            ->get()
            ->map(fn($payment) => $this->transformPayment($payment));

        $paymentAccounts = PaymentAccount::where('is_active', true)->get();

        return Inertia::render('Internal/Payment/PaymentIndex', [
            'payments' => $payments,
            'paymentAccounts' => $paymentAccounts,
            'statuses' => ['pending', 'completed', 'failed', 'refunded']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $paymentAccounts = PaymentAccount::where('is_active', true)->get();
        $payableItems = $this->getPayableItems();

        return Inertia::render('Internal/Payment/PaymentCreate', [
            'paymentAccounts' => $paymentAccounts,
            'payableItems' => $payableItems
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'payment_account_id' => 'required|exists:payment_accounts,id',
            'total_amount' => 'required|numeric|min:0',
            'memo' => 'nullable|string',
            'status' => 'required|in:pending,completed,failed,refunded',
            'payable_id' => 'required|integer',
            'payable_type' => 'required|string',
            'paid_by' => 'required|integer|exists:users,id'
        ]);


        $payment = Payment::create($validated);

        $payment->update([
            'ref_number' => GenerateSequence::generateRefNumber('PAY', 6, $payment->id)
        ]);



        if($request->status === 'completed') {
            UpdateStatusModelPayable::execute(
                $validated['payable_type'],
                $validated['payable_id'],
                'paid'
            );
        }


        return redirect()->route('internal.payments.index')
            ->with('success', 'Payment created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with(['paymentAccount', 'paidBy', 'payable'])
            ->findOrFail($id);

        return Inertia::render('Internal/Payment/PaymentShow', [
            'payment' => $this->transformPayment($payment)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $payment = Payment::with(['paymentAccount', 'paidBy', 'payable'])
            ->findOrFail($id);
        $paymentAccounts = PaymentAccount::where('is_active', true)->get();
        $payableItems = $this->getPayableItems();

        return Inertia::render('Internal/Payment/PaymentEdit', [
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
            'status' => 'required|in:pending,completed,failed,refunded'
        ]);

        $payment->update($validated);

         if($request->status === 'completed') {
            UpdateStatusModelPayable::execute(
                $payment->payable_type,
                $payment->payable_id,
                'paid'
            );
        }

        return redirect()->route('internal.payments.index')
            ->with('success', 'Payment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->route('internal.payments.index')
            ->with('success', 'Payment deleted successfully');
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

    private function getPayableItems()
    {
        $rentalOrders = RentalOrder::with('user')
            ->whereDoesntHave('payments', function($query) {
                $query->where('status', 'completed');
            })
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
}
