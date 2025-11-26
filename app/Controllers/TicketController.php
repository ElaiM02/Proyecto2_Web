<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::all();
        return $this->view('tickets/index', ['tickets' => $tickets]);
    }
}