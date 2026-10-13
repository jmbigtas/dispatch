<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Role', function (Blueprint $t) {
            $t->id('RoleID'); $t->string('RoleName', 100)->unique(); $t->timestamps();
        });
        Schema::create('Division', function (Blueprint $t) {
            $t->id('DivisionID'); $t->string('DivisionName', 100); $t->timestamps();
        });
        Schema::create('Status', function (Blueprint $t) {
            $t->id('StatusID'); $t->string('StatusCategory', 50); $t->string('StatusName', 100); $t->string('StatusDesc', 150)->nullable();
        });
        Schema::create('Vehicles_Models', function (Blueprint $t) {
            $t->id('ModelID'); $t->string('ModelName', 100); $t->string('Type', 100); $t->integer('Capacity'); $t->timestamps();
        });
        Schema::create('Users', function (Blueprint $t) {
            $t->id('UserID');
            $t->foreignId('Division')->constrained('Division', 'DivisionID');
            $t->string('FirstName', 100); $t->string('LastName', 100);
            $t->string('Email', 100)->unique(); $t->string('passwordhash', 255);
            $t->foreignId('Role')->constrained('Role', 'RoleID');
            $t->timestamps();
        });
        Schema::create('Drivers', function (Blueprint $t) {
            $t->id('DriversID'); $t->string('FirstName', 100); $t->string('LastName', 100); $t->string('ContactNo', 13);
            $t->foreignId('Status')->constrained('Status', 'StatusID'); $t->timestamps();
        });
        Schema::create('Vehicles', function (Blueprint $t) {
            $t->id('VehicleID');
            $t->foreignId('ModelID')->constrained('Vehicles_Models', 'ModelID');
            $t->string('PlateNo', 100)->unique();
            $t->foreignId('StatusID')->constrained('Status', 'StatusID'); $t->timestamps();
        });
        Schema::create('Trip_Ticket', function (Blueprint $t) {
            $t->id('tripTicketId');
            $t->dateTime('DateRequested'); $t->dateTime('DatePrepared')->nullable();
            $t->foreignId('RequestedBy')->constrained('Users', 'UserID');
            $t->foreignId('RequestingDivision')->constrained('Division', 'DivisionID');
            $t->integer('numOfPassenger'); $t->string('TripPurpose', 150);
            $t->dateTime('DepartureTime'); $t->dateTime('ArrivalTime')->nullable(); $t->dateTime('ReturnTime')->nullable();
            $t->foreignId('AssignedVehicle')->nullable()->constrained('Vehicles', 'VehicleID');
            $t->foreignId('AssignedDriver')->nullable()->constrained('Drivers', 'DriversID');
            $t->foreignId('Status')->constrained('Status', 'StatusID');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['Trip_Ticket', 'Vehicles', 'Drivers', 'Users', 'Vehicles_Models', 'Status', 'Division', 'Role'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
