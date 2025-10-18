<?php

namespace App\Http\Controllers\Internal;

use Inertia\Inertia;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentAcountController extends Controller
{
    public function index()
    {
        $accounts = PaymentAccount::with('payments')
            ->latest()
            ->get();

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
            'is_active' => 'boolean'
        ]);

        PaymentAccount::create($validated);

        return redirect()->route('internal.payment-accounts.index')
            ->with('success', 'Payment account created successfully');
    }

    public function show(string $id)
    {
        $account = PaymentAccount::with('payments')->findOrFail($id);

        return Inertia::render('Internal/PaymentAccount/PaymentAccountShow', [
            'account' => $account
        ]);
    }

    public function edit(string $id)
    {
        $account = PaymentAccount::findOrFail($id);

        return Inertia::render('Internal/PaymentAccount/PaymentAccountEdit', [
            'account' => $account
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
            'is_active' => 'boolean'
        ]);

        $account->update($validated);

        return redirect()->route('internal.payment-accounts.index')
            ->with('success', 'Payment account updated successfully');
    }

    public function destroy(string $id)
    {
        $account = PaymentAccount::findOrFail($id);
        $account->delete();

        return redirect()->route('internal.payment-accounts.index')
            ->with('success', 'Payment account deleted successfully');
    }
}
