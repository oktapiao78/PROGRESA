<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $contacts = collect();
        $thesis = null;

        if ($user->isMahasiswa()) {
            $thesis = $user->studentThesis()->with(['supervisor1', 'supervisor2'])->first();
            if ($thesis) {
                if ($thesis->supervisor1) {
                    $contacts->push($thesis->supervisor1);
                }
                if ($thesis->supervisor2 && $thesis->supervisor2->id !== $thesis->supervisor1?->id) {
                    $contacts->push($thesis->supervisor2);
                }
            }
        } elseif ($user->isDosen()) {
            $theses = Thesis::with('student')
                ->where('supervisor_1_id', $user->id)
                ->orWhere('supervisor_2_id', $user->id)
                ->get();

            $contacts = $theses->pluck('student')->filter()->unique('id')->values();
        }

        $selectedContact = null;
        if ($request->filled('user_id')) {
            $selectedContact = $contacts->firstWhere('id', (int) $request->query('user_id'));
        }
        if (!$selectedContact && $contacts->isNotEmpty()) {
            $selectedContact = $contacts->first();
        }

        $messages = collect();
        if ($selectedContact) {
            // Tandai pesan dari kontak ini sebagai terbaca
            ChatMessage::where('sender_id', $selectedContact->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $messages = ChatMessage::where(function ($q) use ($user, $selectedContact) {
                $q->where('sender_id', $user->id)->where('receiver_id', $selectedContact->id);
            })->orWhere(function ($q) use ($user, $selectedContact) {
                $q->where('sender_id', $selectedContact->id)->where('receiver_id', $user->id);
            })->orderBy('created_at')->get();
        }

        // Lampirkan ringkasan percakapan terakhir & jumlah belum dibaca per kontak
        $contacts = $contacts->map(function ($contact) use ($user) {
            $contact->last_message = ChatMessage::where(function ($q) use ($user, $contact) {
                $q->where('sender_id', $user->id)->where('receiver_id', $contact->id);
            })->orWhere(function ($q) use ($user, $contact) {
                $q->where('sender_id', $contact->id)->where('receiver_id', $user->id);
            })->latest()->first();

            $contact->unread_count = ChatMessage::where('sender_id', $contact->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            return $contact;
        });

        return view('chat.index', compact('contacts', 'selectedContact', 'messages', 'thesis'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $receiver = User::findOrFail($validated['receiver_id']);
        $allowed = false;
        $thesisId = null;

        if ($user->isMahasiswa()) {
            $thesis = $user->studentThesis;
            if ($thesis && $thesis->isSupervisor($receiver)) {
                $allowed = true;
                $thesisId = $thesis->id;
            }
        } elseif ($user->isDosen()) {
            $thesis = Thesis::where('student_id', $receiver->id)
                ->where(function ($q) use ($user) {
                    $q->where('supervisor_1_id', $user->id)
                      ->orWhere('supervisor_2_id', $user->id);
                })
                ->first();
            if ($thesis) {
                $allowed = true;
                $thesisId = $thesis->id;
            }
        }

        if (!$allowed) {
            abort(403, 'Anda hanya dapat mengirim pesan kepada pembimbing atau mahasiswa bimbingan Anda.');
        }

        ChatMessage::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'thesis_id' => $thesisId,
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        Notification::sendTo(
            $receiver->id,
            'Pesan Baru dari ' . $user->name,
            mb_strimwidth($validated['message'], 0, 100, '...'),
            'chat',
            route('chat.index', ['user_id' => $user->id])
        );

        return redirect()->route('chat.index', ['user_id' => $receiver->id]);
    }
}

