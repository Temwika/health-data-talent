<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;

class EnquiryController extends Controller
{
    public function index()
    {
        return view('admin.enquiries.index', [
            'enquiries' => Enquiry::orderByRaw('handled_at is not null')->latest()->paginate(20),
        ]);
    }

    public function update(Enquiry $enquiry)
    {
        $enquiry->handled_at = $enquiry->handled_at ? null : now();
        $enquiry->save();

        return back()->with('status', $enquiry->handled_at ? 'Marked as handled.' : 'Reopened.');
    }
}
