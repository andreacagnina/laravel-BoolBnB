<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use Carbon\Carbon;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $messages = collect(config('db_messages')); // Collezione di messaggi predefiniti
        $totalMessages = $messages->count(); // Numero totale di messaggi disponibili
        $properties = DB::table('properties')->select('id', 'created_at')->get();

        if ($properties->isEmpty()) {
            $this->command->info('No properties found in the properties table.');
            return;
        }

        foreach ($properties as $property) {
            // Ottieni sponsorizzazioni per la proprietà
            $sponsorships = DB::table('property_sponsor')
                ->where('property_id', $property->id)
                ->get();

            // Determina il numero massimo di messaggi da generare per questa proprietà
            $baseCount = rand(1, 3); // Riduci il numero base di messaggi
            $additionalCount = 0;

            if ($sponsorships->isNotEmpty()) {
                // Riduci l'impatto delle sponsorizzazioni per limitare i messaggi totali
                $additionalCount = rand(2, 5); // Aggiungi pochi messaggi extra
            }

            $messageCount = min($baseCount + $additionalCount, $totalMessages); // Limita al totale disponibile

            for ($i = 0; $i < $messageCount; $i++) {
                // Determina una data per il messaggio
                $messageDate = null;

                if ($sponsorships->isNotEmpty() && rand(1, 100) <= 50) {
                    $randomSponsorship = $sponsorships->random();
                    $messageDate = Carbon::parse($randomSponsorship->created_at)
                        ->addSeconds(rand(0, Carbon::parse($randomSponsorship->end_date)->diffInSeconds($randomSponsorship->created_at)));
                } else {
                    $messageDate = Carbon::parse($property->created_at)
                        ->addSeconds(rand(0, Carbon::now()->diffInSeconds($property->created_at)));
                }

                // Prendi un messaggio casuale dal file di configurazione
                $messageData = $messages->pop(); // Usa e rimuovi un messaggio dalla collezione

                // Se i messaggi sono esauriti, interrompi
                if (!$messageData) {
                    break;
                }

                Message::create([
                    'first_name' => $messageData['first_name'],
                    'last_name' => $messageData['last_name'],
                    'email' => $messageData['email'],
                    'message' => $messageData['message'],
                    'is_read' => $messageData['is_read'],
                    'property_id' => $property->id,
                    'created_at' => $messageDate,
                    'updated_at' => $messageDate,
                ]);
            }
        }
    }
}
