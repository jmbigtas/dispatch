<?php
namespace App\Http\Controllers;
use App\Models\{TripTicket, User};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DriverDashboardController extends Controller
{
    public function mine(Request $r) { return $this->render($r, $r->user(), 'driver.dashboard', []); }

    public function show(Request $r, User $driver)
    {
        abort_unless($driver->hasRole('driver'), 404);
        return $this->render($r, $driver, 'driver.schedule', ['driver' => $driver->UserID]);
    }

    private function render(Request $r, User $driver, string $rn, array $rp)
    {
        $sel = rescue(fn () => Carbon::parse($r->query('date', 'today')), now(), false)->startOfDay();
        $month = $sel->copy()->startOfMonth();

        // Only this driver's approved/completed trips, looked up by their user id.
        $mine = fn () => TripTicket::where('AssignedDriver', $driver->UserID)
            ->whereHas('statusRel', fn ($q) => $q->whereIn('StatusName', ['Approved', 'Completed']));

        $counts = [
            'today' => $mine()->whereBetween('DepartureTime', [today(), today()->endOfDay()])->count(),
            'week' => $mine()->whereBetween('DepartureTime', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => $mine()->whereBetween('DepartureTime', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        $range = in_array($r->query('range'), ['day', 'week', 'month']) ? $r->query('range') : 'day';
        [$from, $to, $label] = match ($range) {
            'day' => [$sel->copy(), $sel->copy()->endOfDay(), $sel->format('l, F j')],
            'week' => [$sel->copy()->startOfWeek(), $sel->copy()->endOfWeek(), $sel->copy()->startOfWeek()->format('M j').' to '.$sel->copy()->endOfWeek()->format('M j')],
            'month' => [$month, $month->copy()->endOfMonth(), $month->format('F Y')],
        };
        $log = $mine()->with(['requester', 'vehicle.vehicleModel', 'divisionRel'])->whereBetween('DepartureTime', [$from, $to])->orderBy('DepartureTime')->get();

        $byDay = $mine()->whereBetween('DepartureTime', [$month, $month->copy()->endOfMonth()])->get()
            ->groupBy(fn ($t) => $t->DepartureTime->toDateString());
        $cells = [];
        for ($d = $month->copy(); $d->month == $month->month; $d->addDay()) {
            $n = $byDay->get($d->toDateString(), collect())->count();
            // none = white, low = light blue (1-2 trips), high = dark blue (3 or more)
            $cells[] = ['date' => $d->copy(), 'trips' => $n, 'level' => $n === 0 ? 'none' : ($n >= 3 ? 'high' : 'low')];
        }

        $current = $mine()->with('vehicle')->where('DepartureTime', '<=', now())
            ->whereRaw('COALESCE(ReturnTime, ArrivalTime, DepartureTime) >= ?', [now()])->first();
        $upcoming = $mine()->with('vehicle')->where('DepartureTime', '>', now())->orderBy('DepartureTime')->limit(5)->get();

        return view('driver.dashboard', compact('driver', 'rn', 'rp', 'sel', 'month', 'counts', 'range', 'label', 'log', 'cells', 'current', 'upcoming'));
    }
}
