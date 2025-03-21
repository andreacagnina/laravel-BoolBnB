<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        // Includiamo i messaggi eliminati e li ordiniamo in modo che quelli attivi appaiano prima
        $messages = Message::withTrashed()
            ->with('property')
            ->whereHas('property', function ($query) {
                $query->where('user_id', Auth::id()); // Filtra per le proprietà dell'utente loggato
            })
            ->orderByRaw('deleted_at IS NULL DESC') // Prima i messaggi non eliminati
            ->orderBy('created_at', 'desc') // Ordine per data di creazione
            ->get();

        $unreadCount = Message::whereHas('property', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('is_read', false)->count();

        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    public function show(Message $message)
    {
        // Blocca l'accesso ai messaggi eliminati
        if (!$message->property || $message->property->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this message.');
        }

        if ($message->trashed()) {
            abort(404, 'Message not found');
        }

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        $unreadCount = Message::whereHas('property', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('is_read', false)->count();

        return view('admin.messages.show', compact('message', 'unreadCount'));
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return redirect()->route('admin.messages.index')->with('success', 'Message deleted successfully');
    }
    public function hardDestroy($id)
    {
        // Recupera il messaggio, inclusi quelli eliminati (soft deleted)
        $message = Message::withTrashed()->find($id);

        // Se il messaggio non esiste, reindirizza con un messaggio di errore
        if (!$message) {
            return redirect()->route('admin.messages.index')->with('error', 'Message not found.');
        }

        try {
            // Esegui l'eliminazione definitiva
            $message->forceDelete();

            // Reindirizza con un messaggio di successo
            return redirect()->route('admin.messages.index')->with('success', 'Message deleted permanently.');
        } catch (\Exception $e) {
            // In caso di errore, reindirizza con un messaggio di errore
            return redirect()->route('admin.messages.index')->with('error', 'Failed to delete message permanently. Please try again.');
        }
    }


    public function restore($id)
    {
        $message = Message::onlyTrashed()->findOrFail($id);
        $message->restore();

        return redirect()->route('admin.messages.index')->with('success', 'Message restored successfully');
    }
}
