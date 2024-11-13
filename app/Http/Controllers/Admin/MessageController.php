<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        // Includiamo i messaggi eliminati e li ordiniamo in modo che quelli attivi appaiano prima
        $messages = Message::withTrashed()
            ->with('property')
            ->orderByRaw('deleted_at IS NULL DESC') // Prima i messaggi non eliminati
            ->orderBy('created_at', 'desc') // Ordine per data di creazione
            ->get();
        $unreadCount = Message::where('is_read', false)->count();
            
        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    public function show(Message $message)
    {
        // Blocca l'accesso ai messaggi eliminati
        if ($message->trashed()) {
            abort(404, 'Message not found');
        }

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

    public function restore($id)
    {
        $message = Message::onlyTrashed()->findOrFail($id);
        $message->restore();

        return redirect()->route('admin.messages.index')->with('success', 'Message restored successfully');
    }
}
