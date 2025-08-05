<?php

namespace App\Http\Controllers;

use App\Models\MedicinePickup;
use App\Models\Patient;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicinePickupController extends Controller
{
    public function index()
    {
        $pickups = MedicinePickup::with(['patient', 'medicine'])
            ->orderBy('scheduled_date', 'asc')
            ->get();

        $patients = Patient::all();
        $medicines = Medicine::all();

        return view('staff.medicine-pickups', compact('pickups', 'patients', 'medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
            'scheduled_date' => 'required|date',
        ]);

        // Check if medicine has enough stock
        $medicine = Medicine::find($request->medicine_id);
        if ($medicine->stock_quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Insufficient stock for this medicine.');
        }

        MedicinePickup::create($request->all());

        return redirect()->back()->with('success', 'Medicine pickup scheduled successfully.');
    }

    public function markAsPickedUp($id)
    {
        $pickup = MedicinePickup::findOrFail($id);

        if ($pickup->status === 'pending') {
            $pickup->status = 'picked_up';
            $pickup->pickup_date = now();
            $pickup->save();

            // Reduce stock
            $medicine = $pickup->medicine;
            $medicine->stock_quantity -= $pickup->quantity;
            $medicine->save();
        }

        return redirect()->back()->with('success', 'Marked as picked up.');
    }

    public function destroy($id)
    {
        $pickup = MedicinePickup::findOrFail($id);
        $pickup->delete();

        return redirect()->back()->with('success', 'Pickup cancelled successfully.');
    }
}
