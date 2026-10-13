<?php
namespace App\Http\Controllers;
use App\Models\{TripTicket, User, Vehicle};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    private function counted() { return TripTicket::whereHas('statusRel', fn ($q) => $q->whereIn('StatusName', ['Approved', 'Completed'])); }

    public function index(Request $r)
    {
        if ($r->user()->hasRole('driver')) return redirect()->route('driver.dashboard');

        $sel = rescue(fn () => Carbon::parse($r->query('date', 'today')), now(), false)->startOfDay();
        $month = $sel->copy()->startOfMonth();
        $drivers = User::drivers()->get();
        $vehicles = Vehicle::with('vehicleModel')->get();
        $byDay = $this->counted()->whereBetween('DepartureTime', [$month, $month->copy()->endOfMonth()])->get()
            ->groupBy(fn ($t) => $t->DepartureTime->toDateString());

        $cells = [];
        for ($d = $month->copy(); $d->month == $month->month; $d->addDay()) {
            $g = $byDay->get($d->toDateString(), collect());
            $busy = $g->pluck('AssignedDriver')->filter()->unique()->count();
            $cells[] = [
                'date' => $d->copy(), 'trips' => $g->count(), 'drivers' => $busy,
                'vehicles' => $g->pluck('AssignedVehicle')->filter()->unique()->count(),
                // none = white, low = light blue, high = dark blue (half or more of the drivers are out)
                'level' => $g->isEmpty() ? 'none' : ($busy >= max(1, ceil($drivers->count() / 2)) ? 'high' : 'low'),
            ];
        }
        $selTrips = $byDay->get($sel->toDateString(), collect());

        return view('dashboard', array_merge(compact('cells', 'month', 'sel', 'selTrips', 'drivers', 'vehicles'), $this->availableNow()));
    }

    // Polled by the dashboard so "available right now" stays current.
    public function available() { return view('partials.available', $this->availableNow()); }

    private function availableNow(): array
    {
        $drivers = User::drivers()->orderBy('FirstName')->get();
        $active = $this->counted()->with('vehicle')->whereNotNull('AssignedDriver')
            ->where('DepartureTime', '<=', now())
            ->whereRaw('COALESCE(ReturnTime, ArrivalTime, DepartureTime) >= ?', [now()])->get()->keyBy('AssignedDriver');
        $next = $this->counted()->whereNotNull('AssignedDriver')->where('DepartureTime', '>', now())
            ->orderBy('DepartureTime')->get()->unique('AssignedDriver')->keyBy('AssignedDriver');

        return [
            'free' => $drivers->reject(fn ($d) => $active->has($d->UserID))->values(),
            'busy' => $drivers->filter(fn ($d) => $active->has($d->UserID))->values(),
            'active' => $active, 'next' => $next,
        ];
    }
}
