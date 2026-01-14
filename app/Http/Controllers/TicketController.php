<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReplay;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $tickets = Ticket::with('replies')->get();

        if ($tickets->isEmpty()) {
            return $this->errorResponse('No tickets found', 404);
        }

        return $this->successResponse($tickets);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'category' => 'required',
            'priority' => 'required',
            'description' => 'required',
        ]);

        $lastTicket = Ticket::latest('id')->first();
        $nextNumber = $lastTicket
            ? ((int) str_replace('T-', '', $lastTicket->ticket_id) + 1)
            : 1001;

        $ticketCode = 'T-'.$nextNumber;

        $ticket = Ticket::create([
            'user_id' => Auth::user()->id,
            'ticket_id' => $ticketCode,
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'description' => $request->description,
            'status' => 'Open',
        ]);

        return $this->successResponse($ticket, 'Ticket created successfully');
    }

    public function show($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return $this->errorResponse('Ticket not found', 404);
        }

        return $this->successResponse($ticket);
    }

    public function reply(Request $request, $id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return $this->errorResponse('Ticket not found', 404);
        }

        TicketReplay::create([
            'ticket_id' => $ticket->id,
            'reply' => $request->reply
        ]);

        return $this->successResponse($ticket);
    }
}
