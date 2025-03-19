<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use Carbon\Carbon;

class MessageSeeder extends Seeder
{
    public function run()
    {
        $messages = collect(config('db_messages'));
        $totalMessages = $messages->count();
        $properties = DB::table('properties')->select('id', 'created_at')->get();

        if ($properties->isEmpty()) {
            $this->command->info('No properties found in the properties table.');
            return;
        }

        foreach ($properties as $property) {
            $sponsorships = DB::table('property_sponsor')
                ->where('property_id', $property->id)
                ->get();

            $baseCount = rand(1, 3);
            $additionalCount = $sponsorships->isNotEmpty() ? rand(2, 5) : 0;

            $messageCount = min($baseCount + $additionalCount, $totalMessages);

            for ($i = 0; $i < $messageCount; $i++) {
                $messageDate = null;

                if ($sponsorships->isNotEmpty() && rand(1, 100) <= 50) {
                    $randomSponsorship = $sponsorships->random();
                    $messageDate = Carbon::parse($randomSponsorship->created_at)
                        ->addSeconds(rand(0, Carbon::parse($randomSponsorship->end_date)->diffInSeconds($randomSponsorship->created_at)));
                } else {
                    $messageDate = Carbon::parse($property->created_at)
                        ->addSeconds(rand(0, Carbon::now()->diffInSeconds($property->created_at)));
                }

                $messageData = $messages->pop();

                if (!$messageData) {
                    break;
                }

                $isRead = $messageDate->lt(Carbon::now()->subMonth())
                    ? true
                    : rand(0, 1) === 1;

                Message::create([
                    'first_name' => $messageData['first_name'],
                    'last_name' => $messageData['last_name'],
                    'email' => $messageData['email'],
                    'message' => $messageData['message'],
                    'is_read' => $isRead,
                    'property_id' => $property->id,
                    'created_at' => $messageDate,
                    'updated_at' => $messageDate,
                ]);
            }
        }
    }
}
