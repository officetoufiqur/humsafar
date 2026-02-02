<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintReply;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $conplaints = Complaint::with('reportedBy:id,fname,lname', 'reportedTo:id,fname,lname')
            ->where('status', '!=', 'resolved')
            ->get();

        if ($conplaints->isEmpty()) {
            return $this->errorResponse('No Complaints Found', 404);
        }

        return $this->successResponse($conplaints, 'Complaints Fetched Successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required',
        ]);

        $complaint = new Complaint;
        $complaint->reported_by = null;
        $complaint->reported_to = Auth::user()->id;
        $complaint->reason = $request->reason;
        $complaint->additional_details = $request->additional_details;
        $complaint->save();

        return $this->successResponse($complaint, 'Complaint Created Successfully');
    }

    public function show($id)
    {
        $complaint = Complaint::with('replies')->findOrFail($id);

        if (! $complaint) {
            return $this->errorResponse('Complaint not found', 404);
        }

        return $this->successResponse($complaint, 'Complaint Fetched Successfully');
    }

    public function storeReplay(Request $request, $complaint_id)
    {
        $request->validate([
            'message' => 'required',
        ]);

        $complaint = Complaint::findOrFail($complaint_id);

        $reply = ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'user_id' => Auth::user()->id,
            'message' => $request->message,
        ]);

        if ($complaint->reported_by == null) {
            $complaint->reported_by = Auth::user()->id;
            $complaint->save();
        }

        return $this->successResponse($reply, 'Reply Created Successfully');
    }

    public function updateBlock($id)
    {
        $complaint = Complaint::find($id);

        if (! $complaint) {
            return $this->errorResponse('Complaint not found', 404);
        }

        $complaint->status = 'blocked';
        $complaint->reported_by = Auth::user()->id;
        $complaint->save();

        return $this->successResponse($complaint, 'Status Updated Successfully');
    }

    public function updateDismiss($id)
    {
        $complaint = Complaint::find($id);

        if (! $complaint) {
            return $this->errorResponse('Complaint not found', 404);
        }

        $complaint->status = 'resolved';
        $complaint->reported_by = Auth::user()->id;
        $complaint->save();

        return $this->successResponse($complaint, 'Status Updated Successfully');
    }
}
