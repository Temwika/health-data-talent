<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Organisation;
use Illuminate\Http\Request;

class OrganisationController extends Controller
{
    public function index()
    {
        return view('admin.employers.index', [
            'organisations' => Organisation::latest()->paginate(20),
        ]);
    }

    public function update(Request $request, Organisation $organisation)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,approved,declined']]);

        $organisation->status = $data['status'];
        $organisation->save();

        AuditLog::record('employer.'.$data['status'], $organisation, $organisation->name.' marked '.$data['status']);

        return back()->with('status', $organisation->name.' marked '.$data['status'].'.');
    }

    public function destroy(Organisation $organisation)
    {
        $name = $organisation->name;
        $organisation->delete();

        AuditLog::record('employer.deleted', null, 'Deleted employer '.$name);

        return back()->with('status', 'Employer deleted.');
    }
}
