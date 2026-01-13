<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use ApiResponse;

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
            'ticket_id' => $ticketCode,
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'description' => $request->description,
            'status' => 'Open',
        ]);

        return $this->successResponse($ticket, 'Ticket created successfully');
    }
}
