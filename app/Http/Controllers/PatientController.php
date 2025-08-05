<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $patients = Patient::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('contact_number', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        })->get(); 
        return view('staff.patientsmngmt', compact('patients'));
    }




    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'gender' => 'required|string',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        Patient::create($request->all());

        return redirect()->back()->with('success', 'Patient added successfully!');
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return response()->json($patient);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'gender' => 'required|string',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        $patient = Patient::findOrFail($id);
        $patient->update($request->all());

        return redirect()->back()->with('success', 'Patient updated successfully!');
    }


    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return redirect()->back()->with('success', 'Patient deleted successfully!');
    }






}
