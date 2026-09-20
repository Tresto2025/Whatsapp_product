<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reconstructed from the production schema (infosuzn_tatkal2.sql) rather than
     * the Laravel skeleton: this table carries the doctor/admin profile fields the
     * application actually reads. Guarded so it is a no-op on the legacy database,
     * which already has the table and has this migration recorded as run.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('show_password')->nullable();
            $table->integer('role')->default(2)->comment('1=admin,2=doctor,3=user');
            $table->text('profession_type')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('profile_image')->nullable();
            $table->text('experience')->nullable();
            $table->text('city')->nullable();
            $table->string('gender', 100)->nullable();
            $table->text('address')->nullable();
            $table->text('tax_details')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('gst_number')->nullable();
            $table->rememberToken();
            $table->integer('status')->default(1);
            $table->integer('booking_enabled')->default(1)->comment('0 = disable, 1 = enable');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('appointment_mode')->default(1)->comment('1= multiple, 2= single');
            $table->text('service_template_id')->nullable();
            $table->text('timing_template_id')->nullable();
            $table->string('slot_type', 200)->nullable();
            $table->string('slot_gap', 200)->nullable();
            $table->text('timing_template_id_1')->nullable();
            $table->text('timing_template_id_2')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable()->useCurrent();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
