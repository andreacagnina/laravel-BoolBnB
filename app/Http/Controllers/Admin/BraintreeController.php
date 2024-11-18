<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Braintree\Gateway;
use App\Models\Property;
use App\Models\Sponsor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BraintreeController extends Controller
{
    protected $gateway;

    public function __construct()
    {
        $this->gateway = new Gateway([
            'environment' => config('services.braintree.environment'),
            'merchantId' => config('services.braintree.merchantId'),
            'publicKey' => config('services.braintree.publicKey'),
            'privateKey' => config('services.braintree.privateKey')
        ]);
    }

    public function checkout(Request $request)
    {
        try {
            $nonce = $request->payment_method_nonce;
            $propertySlug = $request->property_slug;
            $sponsorId = $request->sponsor_id;
    
            $property = Property::where('slug', $propertySlug)->firstOrFail();
            $sponsor = Sponsor::findOrFail($sponsorId);
    
            $result = $this->gateway->transaction()->sale([
                'amount' => $sponsor->price,
                'paymentMethodNonce' => $nonce,
                'options' => [
                    'submitForSettlement' => true
                ]
            ]);
    
            if ($result->success) {
                // Verifica la sponsorizzazione precedente
                $lastSponsorPivot = $property->sponsors()
                    ->withPivot('end_date')
                    ->orderByPivot('created_at', 'desc')
                    ->first();
    
                $now = Carbon::now();
    
                if ($lastSponsorPivot && $lastSponsorPivot->pivot->end_date > $now) {
                    // Sponsorizzazione attiva, inizia dopo la fine
                    $startDate = Carbon::parse($lastSponsorPivot->pivot->end_date);
                } else {
                    // Nessuna sponsorizzazione attiva, inizia ora
                    $startDate = $now;
                }
    
                // Calcola la data di fine
                $endDate = $startDate->copy()->addHours($sponsor->duration);
    
                // Associa lo sponsor
                $property->sponsors()->attach($sponsorId, [
                    'created_at' => $startDate,
                    'updated_at' => now(),
                    'end_date' => $endDate
                ]);
    
                // Aggiorna lo stato sponsorizzato
                $property->sponsored = true;
                $property->save();
    
                return redirect()->route('admin.sponsors.show', $property->slug)
                    ->with('success', 'Sponsorship added successfully!');
            } else {
                Log::error('Braintree transaction failed:', ['message' => $result->message]);
                return back()->withErrors('Payment failed: ' . $result->message);
            }
        } catch (\Exception $e) {
            Log::error('Exception during Braintree checkout:', ['error' => $e->getMessage()]);
            return back()->withErrors('An error occurred during the transaction. Please try again.');
        }
    }
    
    public function token()
    {
        try {
            $clientToken = $this->gateway->clientToken()->generate();
            return response()->json(['token' => $clientToken]);
        } catch (\Exception $e) {
            Log::error('Error generating Braintree client token:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Unable to generate client token'], 500);
        }
    }
}
