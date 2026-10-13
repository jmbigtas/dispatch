<?php
namespace App\Http\Controllers;
use App\Models\{Driver, TripTicket, Vehicle};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        $u = $r->user();
        $sel = rescue(fn () => Carbon::parse($r->query('date', 'today')), now(), false)->startOfDay();
        $month = $sel->copy()->startOfMonth();

        // A trip "counts" once it is approved or completed.
        $base = fn () => TripTicket::whereHas('statusRel', fn ($q) => $q->whereIn('StatusName', ['Approved', 'Completed']));
        $mine = fn () => $base()->when($u->hasRole('user'), fn ($q) => $q->where('RequestedBy', $u->UserID));
        $span = fn ($k) => match ($k) {
            'today' => [today(), today()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
        };

        $counts = collect(['today', 'week', 'month'])->mapWithKeys(fn ($k) => [$k => $mine()->whereBetween('DepartureTime', $span($k))->count()]);
        $range = in_array($r->query('range'), ['today', 'week', 'month']) ? $r->query('range') : 'today';
        $log = $mine()->with(['requester', 'vehicle.vehicleModel', 'driver', 'divisionRel'])
            ->whereBetween('DepartureTime', $span($range))->orderBy('DepartureTime')->get();

        // Calendar is fleet-wide so everyone sees real availability.
        $drivers = Driver::all();
        $vehicles = Vehicle::with('vehicleModel')->get();
        $byDay = $base()->whereBetween('DepartureTime', [$month, $month->copy()->endOfMonth()])->get()
            ->groupBy(fn ($t) => $t->DepartureTime->toDateString());

        $cells = [];
        for ($d = $month->copy(); $d->month == $month->month; $d->addDay()) {
            $g = $byDay->get($d->toDateString(), collect());
            $busy = $g->pluck('AssignedDriver')->filter()->unique()->count();
            $cells[] = [
                'date' => $d->copy(), 'trips' => $g->count(), 'drivers' => $busy,
                'vehicles' => $g->pluck('AssignedVehicle')->filter()->unique()->count(),
                // none = white, low = light blue, high = dark blue (half or more of drivers are out)
                'level' => $g->isEmpty() ? 'none' : ($busy >= max(1, ceil($drivers->count() / 2)) ? 'high' : 'low'),
            ];
        }
        $selTrips = $byDay->get($sel->toDateString(), collect());

        return view('dashboard', compact('counts', 'range', 'log', 'cells', 'month', 'sel', 'selTrips', 'drivers', 'vehicles'));
    }
}
