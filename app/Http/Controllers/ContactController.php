<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
        $thanks = 'Thanks for your message. We reply within one working day.';

        if ($request->filled('website')) {
            return redirect()->route('contact.create')->with('status', $thanks);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:160'],
            'organisation' => ['nullable', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:3000'],
            'privacy' => ['accepted'],
        ], [
            'privacy.accepted' => 'Please confirm to continue.',
        ]);

        Enquiry::create($data);

        return redirect()->route('contact.create')->with('status', $thanks);
    }
}
