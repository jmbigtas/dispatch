<?php
namespace App\Http\Controllers;
use App\Models\{Division, Driver, Status, TripTicket, Vehicle};
use Illuminate\Http\Request;

class TripTicketController extends Controller
{
    public function index(Request $r)
    {
        $trips = TripTicket::with(['requester', 'vehicle', 'driver', 'statusRel'])
            ->when($r->user()->hasRole('user'), fn ($q) => $q->where('RequestedBy', $r->user()->UserID))
            ->latest('DateRequested')->paginate(15);
        $statuses = Status::where('StatusCategory', 'Trip')->get();
        return view('trips.index', compact('trips', 'statuses'));
    }

    public function create() { return view('trips.create', ['divisions' => Division::all()]); }

    public function store(Request $r)
    {
        $d = $r->validate([
            'RequestingDivision' => 'required|exists:Division,DivisionID',
            'numOfPassenger' => 'required|integer|min:1|max:60',
            'TripPurpose' => 'required|string|max:150',
            'DepartureTime' => 'required|date|after:now',
            'ArrivalTime' => 'nullable|date|after:DepartureTime',
            'ReturnTime' => 'required|date|after:DepartureTime',
        ]);
        TripTicket::create($d + [
            'DateRequested' => now(), 'RequestedBy' => $r->user()->UserID,
            'Status' => Status::idOf('Trip', 'Pending'),
        ]);
        return redirect()->route('trips.index')->with('ok', 'Trip request submitted for approval.');
    }

    public function review(TripTicket $trip)
    {
        abort_unless($trip->statusRel->StatusName === 'Pending', 404);
        $busyV = $this->overlapping($trip, 'AssignedVehicle');
        $busyD = $this->overlapping($trip, 'AssignedDriver');
        $vehicles = Vehicle::with('vehicleModel')->whereNotIn('VehicleID', $busyV)
            ->whereHas('statusRel', fn ($q) => $q->where('StatusName', 'Available'))
            ->whereHas('vehicleModel', fn ($q) => $q->where('Capacity', '>=', $trip->numOfPassenger))->get();
        $drivers = Driver::whereNotIn('DriversID', $busyD)
            ->whereHas('statusRel', fn ($q) => $q->where('StatusName', 'Available'))->get();
        return view('trips.review', compact('trip', 'vehicles', 'drivers'));
    }

    public function approve(Request $r, TripTicket $trip)
    {
        $d = $r->validate(['AssignedVehicle' => 'required|exists:Vehicles,VehicleID', 'AssignedDriver' => 'required|exists:Drivers,DriversID']);
        if ($this->overlapping($trip, 'AssignedVehicle')->contains($d['AssignedVehicle'])
            || $this->overlapping($trip, 'AssignedDriver')->contains($d['AssignedDriver'])) {
            return back()->withErrors(['AssignedDriver' => 'That driver or vehicle was just booked for this time.']);
        }
        $trip->update($d + ['DatePrepared' => now(), 'Status' => Status::idOf('Trip', 'Approved')]);
        return redirect()->route('trips.index')->with('ok', "Trip #{$trip->tripTicketId} approved.");
    }

    public function reject(TripTicket $trip)
    {
        $trip->update(['Status' => Status::idOf('Trip', 'Rejected'), 'DatePrepared' => now()]);
        return redirect()->route('trips.index')->with('ok', "Trip #{$trip->tripTicketId} rejected.");
    }

    // Change the status of any ticket, including after approval (e.g. Approved -> Completed or Cancelled).
    public function updateStatus(Request $r, TripTicket $trip)
    {
        $d = $r->validate(['Status' => 'required|exists:Status,StatusID']);
        $new = Status::findOrFail($d['Status']);
        abort_unless($new->StatusCategory === 'Trip', 422);

        if (in_array($new->StatusName, ['Approved', 'Completed'])) {
            if (! $trip->AssignedVehicle || ! $trip->AssignedDriver) {
                return back()->withErrors(['Status' => "Trip #{$trip->tripTicketId} has no vehicle and driver. Set it to Pending, then review and approve it."]);
            }
            if ($this->overlapping($trip, 'AssignedVehicle')->contains($trip->AssignedVehicle)
                || $this->overlapping($trip, 'AssignedDriver')->contains($trip->AssignedDriver)) {
                return back()->withErrors(['Status' => "Trip #{$trip->tripTicketId} conflicts with another approved trip for its driver or vehicle."]);
            }
        }
        $trip->update(['Status' => $new->StatusID]);
        return back()->with('ok', "Trip #{$trip->tripTicketId} is now {$new->StatusName}.");
    }

    // IDs of vehicles/drivers already booked on an approved trip that overlaps this one in time.
    private function overlapping(TripTicket $trip, string $col)
    {
        return TripTicket::whereHas('statusRel', fn ($q) => $q->whereIn('StatusName', ['Approved', 'Completed']))
            ->where('tripTicketId', '!=', $trip->tripTicketId)
            ->where('DepartureTime', '<', $trip->ReturnTime)->where('ReturnTime', '>', $trip->DepartureTime)
            ->whereNotNull($col)->pluck($col);
    }
}
