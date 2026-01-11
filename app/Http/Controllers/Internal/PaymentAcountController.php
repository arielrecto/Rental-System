<?php

namespace App\Http\Controllers\Internal;

use Inertia\Inertia;
use App\Models\Attachment;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PaymentAcountController extends Controller
{
    public function index()
    {
        $accounts = PaymentAccount::with(['payments', 'attachments'])
            ->latest()
            ->get()
            ->map(fn($account) => $this->transformAccount($account));

        return Inertia::render('Internal/PaymentAccount/PaymentAccountIndex', [
            'accounts' => $accounts
        ]);
    }

    public function create()
    {
        return Inertia::render('Internal/PaymentAccount/PaymentAccountCreate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_number' => 'required|string|max:255|unique:payment_accounts',
            'account_name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'descriptions' => 'nullable|string',
            'is_active' => 'boolean',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $account = PaymentAccount::create([
            'account_number' => $validated['account_number'],
            'account_name' => $validated['account_name'],
            'provider' => $validated['provider'],
            'descriptions' => $validated['descriptions'] ?? null,
            'is_active' => $validated['is_active'] ?? true
        ]);

        // Handle QR code upload
        if ($request->hasFile('qr_code')) {
            $this->uploadQrCode($request->file('qr_code'), $account);
        }

        return redirect()->route('internal.payment-accounts.index')
            ->with('success', 'Payment account created successfully');
    }

    public function show(string $id)
    {
        $account = PaymentAccount::with(['payments', 'attachments'])->findOrFail($id);

        return Inertia::render('Internal/PaymentAccount/PaymentAccountShow', [
            'account' => $this->transformAccount($account)
        ]);
    }

    public function edit(string $id)
    {
        $account = PaymentAccount::with('attachments')->findOrFail($id);

        return Inertia::render('Internal/PaymentAccount/PaymentAccountEdit', [
            'account' => $this->transformAccount($account)
        ]);
    }

    public function update(Request $request, string $id)
    {
        $account = PaymentAccount::findOrFail($id);

        $validated = $request->validate([
            'account_number' => 'required|string|max:255|unique:payment_accounts,account_number,'.$id,
            'account_name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'descriptions' => 'nullable|string',
            'is_active' => 'boolean',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'remove_qr' => 'nullable|boolean'
        ]);

        $account->update([
            'account_number' => $validated['account_number'],
            'account_name' => $validated['account_name'],
            'provider' => $validated['provider'],
            'descriptions' => $validated['descriptions'] ?? null,
            'is_active' => $validated['is_active'] ?? true
        ]);

        // Handle QR removal
        if ($request->has('remove_qr') && $request->remove_qr) {
            $this->removeQrCode($account);
        }

        // Handle new QR upload
        if ($request->hasFile('qr_code')) {
            // Remove old QR first
            $this->removeQrCode($account);
            // Upload new QR
            $this->uploadQrCode($request->file('qr_code'), $account);
        }

        return redirect()->route('internal.payment-accounts.index')
            ->with('success', 'Payment account updated successfully');
    }

    public function destroy(string $id)
    {
        $account = PaymentAccount::findOrFail($id);
        
        // Remove QR code before deleting
        $this->removeQrCode($account);
        
        $account->delete();

        return redirect()->route('internal.payment-accounts.index')
            ->with('success', 'Payment account deleted successfully');
    }

    private function uploadQrCode($file, $account)
    {
        $fileName = 'QR-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('payment-accounts/qr-codes', $fileName, 'public');

        Attachment::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_extension' => $file->getClientOriginalExtension(),
            'attachable_id' => $account->id,
            'attachable_type' => PaymentAccount::class
        ]);
    }

    private function removeQrCode($account)
    {
        $qrCode = $account->attachments()->first();
        if ($qrCode) {
            Storage::disk('public')->delete($qrCode->file_path);
            $qrCode->delete();
        }
    }

    private function transformAccount($account)
    {
        $qrCode = $account->attachments->first();

        return [
            'id' => $account->id,
            'account_number' => $account->account_number,
            'account_name' => $account->account_name,
            'provider' => $account->provider,
            'descriptions' => $account->descriptions,
            'is_active' => $account->is_active,
            'created_at' => $account->created_at,
            'updated_at' => $account->updated_at,
            'payments_count' => $account->payments->count(),
            'qr_code' => $qrCode ? [
                'id' => $qrCode->id,
                'file_name' => $qrCode->file_name,
                'file_path' => asset('storage/' . $qrCode->file_path),
                'file_type' => $qrCode->file_type ?? 'image',
                'file_size' => $qrCode->file_size ?? 0,
            ] : null,
        ];
    }
}
