<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Server;
use App\Services\ServerManager;
use Carbon\Carbon;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        if ($payload['event_type'] !== 'PAYMENT.SALE.COMPLETED') {
            return response()->json(['status' => 'ignored']);
        }

        $invoiceId = $payload['resource']['custom_id'] ?? $payload['resource']['invoice_id'];
        $invoice = Invoice::find($invoiceId);

        if (!$invoice || $invoice->status === 'paid') {
            return response()->json(['status' => 'failed'], 400);
        }

        Payment::create([
            'invoice_id' => $invoice->id,
            'gateway' => 'paypal',
            'transaction_id' => $payload['resource']['id'],
            'amount' => $payload['resource']['amount']['total'],
            'status' => 'completed',
        ]);

        $invoice->update(['status' => 'paid']);

        $server = Server::where('user_id', $invoice->user_id)
            ->where('package_id', $invoice->package_id)
            ->first();

        if ($server) {
            $server->update([
                'expires_at' => Carbon::now()->addMonth(),
                'status' => 'active'
            ]);
            $driver = ServerManager::make($server->driver);
            $driver->unsuspendServer($server->external_id);
        }

        return response()->json(['status' => 'success']);
    }
}