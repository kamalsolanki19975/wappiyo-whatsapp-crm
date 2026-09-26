<?php

namespace App\Http\Controllers\User;

use DB;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Resources\TicketResource;
use App\Http\Requests\StoreTicket;
use App\Http\Requests\StoreTicketComment;
use App\Http\Requests\StoreTicketStatus;
use App\Http\Requests\StoreTicketPriority;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TicketController extends BaseController
{
    private $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index(Request $request, $uuid = null){
        if($uuid === null){
            $userId = auth()->user()->id;
            $query = Ticket::with(['category', 'user', 'agent'])
                ->where('user_id', $userId);

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('priority') && $request->priority !== 'all') {
                $query->where('priority', $request->priority);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                      ->orWhere('reference', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            }

            $rows = TicketResource::collection(
                $query->latest()->paginate(10)->withQueryString()
            );

            $counts = [
                'total' => Ticket::where('user_id', $userId)->count(),
                'open' => Ticket::where('user_id', $userId)->where('status', 'open')->count(),
                'pending' => Ticket::where('user_id', $userId)->where('status', 'pending')->count(),
                'resolved' => Ticket::where('user_id', $userId)->where('status', 'resolved')->count(),
                'closed' => Ticket::where('user_id', $userId)->where('status', 'closed')->count(),
            ];

            return Inertia::render('User/Support/Index', [
                'title' => __('Support'),
                'allowCreate' => true,
                'rows' => $rows,
                'filters' => $request->all(),
                'counts' => $counts,
            ]);
        } else if($uuid === 'create'){
            $data['categories'] = TicketCategory::get();
            $data['title'] = __('Create ticket');
            return Inertia::render('User/Support/Create', $data);
        } else {
            $user = auth()->user();
            $ticketQuery = Ticket::with(['commentsWithUser', 'category', 'user', 'agent'])->where('uuid', $uuid);
            if ($user && $user->role === 'user') {
                $ticketQuery->where('user_id', $user->id);
            }
            $ticket = $ticketQuery->first();
            if (!$ticket) {
                abort(404);
            }
            return Inertia::render('User/Support/View', [
                'title' => __('View ticket'),
                'ticket' => $ticket
            ]);
        }
    }

    public function store(StoreTicket $request){
        $this->ticketService->store($request);

        return Redirect::route('support')->with(
            'status', [
                'type' => 'success', 
                'message' => __('Ticket created successfully')
            ]
        );
    }

    public function comment(StoreTicketComment $request, $ticketUuid){
        $this->ticketService->comment($request, $ticketUuid);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Comment added successfully')
            ]
        );
    }

    public function changeStatus(StoreTicketStatus $request, $ticketUuid){
        $this->ticketService->changeStatus($request, $ticketUuid);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Ticket updated successfully')
            ]
        );
    }

    public function changePriority(StoreTicketPriority $request, $ticketUuid){
        $this->ticketService->changePriority($request, $ticketUuid);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Ticket priority updated successfully')
            ]
        );
    }
}