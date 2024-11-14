<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use App\Models\Message;
use Carbon\Carbon;

class MessageSeeder extends Seeder
{
    /**
     * Esegui i seeds per il database.
     *
     * @return void
     */
    public function run()
    {
        // Carica i messaggi dal file di configurazione db_messages.php
        $messages = config('db_messages');

        foreach ($messages as $messageData) {
            $message = new Message();
            $message->first_name = $messageData['first_name'];
            $message->last_name = $messageData['last_name'];
            $message->email = $messageData['email'];
            $message->message = $messageData['message'];
            $message->is_read = $messageData['is_read'];
            $message->property_id = $messageData['property_id'];

            // Quanti giorni fa è stato creato il messaggio
            $date = Carbon::now()->subDays(rand(0, 40));
            $message->created_at = $date;
            $message->updated_at = $date;

            $message->save();
        }
    }
}
