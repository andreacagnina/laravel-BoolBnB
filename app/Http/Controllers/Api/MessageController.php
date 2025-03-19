<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with('property')->get();
        return response()->json($messages);
    }

    public function show(Message $message)
    {
        return response()->json($message->load('property'));
    }

    public function store(Request $request)
    {
        $message = Message::create($request->all());
        return response()->json($message, 201);
    }

    public function update(Request $request, Message $message)
    {
        $message->update($request->all());
        return response()->json($message);
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return response()->json(['message' => 'Message deleted successfully'], 200);
    }
}
