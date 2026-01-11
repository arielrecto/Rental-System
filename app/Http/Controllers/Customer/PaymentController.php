<?php

namespace App\Http\Controllers\Customer;

use Inertia\Inertia;
use App\Models\Payment;
use App\Models\Attachment;
use App\Models\RentalOrder;
use Illuminate\Http\Request;
use App\Models\PaymentAccount;
use App\Actions\GenerateSequence;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Actions\UpdateStatusModelPayable;

class PaymentController extends Controller
{
    public function history(Request $request)
    {
        $query = Payment::with(['paymentAccount', 'paidBy', 'payable', 'attachments'])
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

        $paymentAccounts = PaymentAccount::with('attachments')
            ->where('is_active', true)
            ->get()
            ->map(fn($account) => $this->transformPaymentAccount($account));

        return Inertia::render('Customer/Payment/PaymentHistory', [
            'payments' => $payments,
            'paymentAccounts' => $paymentAccounts,
            'statuses' => ['pending', 'completed', 'failed', 'refunded']
        ]);
    }

    public function create()
    {
        $paymentAccounts = PaymentAccount::with('attachments')
            ->where('is_active', true)
            ->get()
            ->map(fn($account) => $this->transformPaymentAccount($account));
            
        $payableItems = $this->getPayableItems();

        return Inertia::render('Customer/Payment/PaymentCreate', [
            'paymentAccounts' => $paymentAccounts,
            'payableItems' => $payableItems
        ]);
    }

    public function edit(string $id)
    {
        $payment = Payment::with(['paymentAccount.attachments', 'paidBy', 'payable', 'attachments'])
            ->findOrFail($id);
            
        $paymentAccounts = PaymentAccount::with('attachments')
            ->where('is_active', true)
            ->get()
            ->map(fn($account) => $this->transformPaymentAccount($account));
            
        $payableItems = $this->getPayableItems();

        return Inertia::render('Customer/Payment/PaymentEdit', [
            'payment' => $this->transformPayment($payment),
            'paymentAccounts' => $paymentAccounts,
            'payableItems' => $payableItems
        ]);
    }

    public function update(Request $request, string $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'payment_account_id' => 'required|exists:payment_accounts,id',
            'total_amount' => 'required|numeric|min:0',
            'memo' => 'nullable|string',
            'status' => 'required|in:pending',
            'proof_of_payment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $payment->update([
            'payment_account_id' => $validated['payment_account_id'],
            'total_amount' => $validated['total_amount'],
            'memo' => $validated['memo'],
            'status' => $validated['status']
        ]);

        // Handle proof of payment upload
        if ($request->hasFile('proof_of_payment')) {
            // Delete old proof if exists
            $oldProof = $payment->attachments()->first();
            if ($oldProof) {
                Storage::disk('public')->delete($oldProof->file_path);
                $oldProof->delete();
            }

            $file = $request->file('proof_of_payment');
            $fileName = 'PROOF-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payments/proofs', $fileName, 'public');

            Attachment::create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'file_extension' => $file->getClientOriginalExtension(),
                'attachable_id' => $payment->id,
                'attachable_type' => get_class($payment)
            ]);
        }

        return redirect()->route('customer.payments.history')
            ->with('success', 'Payment updated successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_account_id' => 'required|exists:payment_accounts,id',
            'total_amount' => 'required|numeric|min:0',
            'memo' => 'nullable|string',
            'status' => 'required|in:pending,completed,failed,refunded',
            'payable_id' => 'required|integer',
            'payable_type' => 'required|string',
            'paid_by' => 'required|integer|exists:users,id',
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $payment = Payment::create([
            'payment_account_id' => $validated['payment_account_id'],
            'total_amount' => $validated['total_amount'],
            'memo' => $validated['memo'],
            'status' => $validated['status'],
            'payable_id' => $validated['payable_id'],
            'payable_type' => $validated['payable_type'],
            'paid_by' => $validated['paid_by']
        ]);

        $payment->update([
            'ref_number' => GenerateSequence::generateRefNumber('PAY', 6, $payment->id)
        ]);

        // Handle proof of payment upload
        if ($request->hasFile('proof_of_payment')) {
            $file = $request->file('proof_of_payment');
            $fileName = 'PROOF-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payments/proofs', $fileName, 'public');

            Attachment::create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'file_extension' => $file->getClientOriginalExtension(),
                'attachable_id' => $payment->id,
                'attachable_type' => Payment::class
            ]);
        }

        UpdateStatusModelPayable::execute(
            $validated['payable_type'],
            $validated['payable_id'],
            'In Payment'
        );

        return redirect()->route('customer.payments.history')
            ->with('success', 'Payment created successfully');
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);

        // Delete associated attachments
        foreach ($payment->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $payment->delete();

        return redirect()->route('customer.payments.history')
            ->with('success', 'Payment deleted successfully');
    }

    private function transformPaymentAccount($account)
    {
        $qrCode = $account->attachments->first();

        return [
            'id' => $account->id,
            'account_number' => $account->account_number,
            'account_name' => $account->account_name,
            'provider' => $account->provider,
            'descriptions' => $account->descriptions,
            'is_active' => $account->is_active,
            'qr_code' => $qrCode ? [
                'id' => $qrCode->id,
                'file_name' => $qrCode->file_name,
                'file_path' => asset('storage/' . $qrCode->file_path),
                'file_type' => $qrCode->file_type ?? 'image',
                'file_size' => $qrCode->file_size ?? 0,
            ] : null,
        ];
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

        return [
            'rental_orders' => $rentalOrders,
        ];
    }

    private function transformPayment($payment)
    {
        // Get the first attachment (proof of payment)
        $proofOfPayment = $payment->attachments->first();

        return [
            'id' => $payment->id,
            'ref_number' => $payment->ref_number,
            'total_amount' => $payment->total_amount,
            'status' => $payment->status,
            'memo' => $payment->memo,
            'created_at' => $payment->created_at,
            'proof_of_payment' => $proofOfPayment ? [
                'id' => $proofOfPayment->id,
                'file_name' => $proofOfPayment->file_name,
                'file_path' => asset('storage/' . $proofOfPayment->file_path),
                'file_type' => $proofOfPayment->file_type ?? 'image',
                'file_size' => $proofOfPayment->file_size ?? 0,
            ] : null,
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
                    'customer' => $payable->user->name ?? 'N/A',
                    'date' => $payable->rental_date ?? null,
                ];
            default:
                return [
                    'id' => $payable->id,
                    'type' => $type,
                    'ref_number' => $payable->ref_number ?? 'N/A',
                ];
        }
    }
}
