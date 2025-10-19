<?php

use App\Enums\TravelRequestStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('requester_name');
            $table->string('destination');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 20)->default(TravelRequestStatus::SOLICITADO->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_requests');
    }
};
