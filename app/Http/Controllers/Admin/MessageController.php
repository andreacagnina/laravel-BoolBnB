<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::orderBy('created_at', 'desc')->with('property')->get();
        $unreadCount = Message::where('is_read', false)->count();
        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    public function show(Message $message)
    {
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }
        $unreadCount = Message::where('is_read', false)->count();
        return view('admin.messages.show', compact('message', 'unreadCount'));
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return redirect()->route('admin.messages.index')->with('success', 'Message deleted successfully');
    }
}
