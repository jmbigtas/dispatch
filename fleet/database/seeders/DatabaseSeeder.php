<?php
namespace Database\Seeders;
use App\Models\{Division, Driver, Role, Status, TripTicket, User, Vehicle, VehicleModel};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['user', 'approving_authority', 'admin'] as $n) Role::create(['RoleName' => $n]);
        $divs = collect(['Administrative', 'Finance', 'Operations', 'IT'])->map(fn ($n) => Division::create(['DivisionName' => $n]));

        foreach ([['Trip', 'Pending'], ['Trip', 'Approved'], ['Trip', 'Rejected'], ['Trip', 'Completed'],
                  ['Vehicle', 'Available'], ['Vehicle', 'Maintenance'], ['Driver', 'Available'], ['Driver', 'On Leave'], ['Trip', 'Cancelled']] as [$c, $n]) {
            Status::create(['StatusCategory' => $c, 'StatusName' => $n, 'StatusDesc' => "$c is $n"]);
        }

        $sedan = VehicleModel::create(['ModelName' => 'Toyota Vios', 'Type' => 'Sedan', 'Capacity' => 4]);
        $van = VehicleModel::create(['ModelName' => 'Toyota Hiace', 'Type' => 'Van', 'Capacity' => 14]);
        foreach ([[$sedan, 'ABC 1234'], [$sedan, 'ABC 2345'], [$van, 'XYZ 3456'], [$van, 'XYZ 4567']] as [$m, $plate]) {
            Vehicle::create(['ModelID' => $m->ModelID, 'PlateNo' => $plate, 'StatusID' => Status::idOf('Vehicle', 'Available')]);
        }
        foreach ([['Juan', 'Dela Cruz'], ['Pedro', 'Santos'], ['Mario', 'Reyes'], ['Luis', 'Garcia'], ['Ramon', 'Cruz']] as $i => [$f, $l]) {
            Driver::create(['FirstName' => $f, 'LastName' => $l, 'ContactNo' => '+6391700000'.$i.'0', 'Status' => Status::idOf('Driver', 'Available')]);
        }

        foreach ([['Admin', 'User', 'admin@example.com', 'admin'], ['Approving', 'Officer', 'approver@example.com', 'approving_authority'],
                  ['Regular', 'User', 'user@example.com', 'user']] as [$f, $l, $e, $r]) {
            User::create(['FirstName' => $f, 'LastName' => $l, 'Email' => $e, 'passwordhash' => 'password',
                'Role' => Role::where('RoleName', $r)->value('RoleID'), 'Division' => $divs->first()->DivisionID]);
        }

        // Sample approved trips so the calendar has colour from day one.
        $requester = User::where('Email', 'user@example.com')->first();
        for ($i = 0; $i < 16; $i++) {
            $dep = now()->startOfMonth()->addDays(rand(0, 27))->setHour(rand(6, 14))->setMinute(0);
            TripTicket::create([
                'DateRequested' => $dep->copy()->subDays(3), 'DatePrepared' => $dep->copy()->subDays(2),
                'RequestedBy' => $requester->UserID, 'RequestingDivision' => $requester->Division,
                'numOfPassenger' => rand(1, 4), 'TripPurpose' => collect(['Site visit', 'Document delivery', 'Client meeting', 'Training'])->random(),
                'DepartureTime' => $dep, 'ArrivalTime' => $dep->copy()->addHour(), 'ReturnTime' => $dep->copy()->addHours(5),
                'AssignedVehicle' => Vehicle::inRandomOrder()->value('VehicleID'), 'AssignedDriver' => Driver::inRandomOrder()->value('DriversID'),
                'Status' => Status::idOf('Trip', 'Approved'),
            ]);
        }
    }
}
