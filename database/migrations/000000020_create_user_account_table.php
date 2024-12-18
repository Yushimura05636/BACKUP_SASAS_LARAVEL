<?php

use App\Models\User_Privilege;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_account', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('status_id'); // Foreign key column
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('password');
            $table->string('email')->unique();
            $table->unsignedBigInteger('phone_number')->unique();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('notes')->nullable();


            $table->foreign('status_id')->references('id')->on('user_account_status')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employee')->onDelete('cascade');


            //not sure here
           // $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');

            $table->unique(['last_name', 'middle_name', 'first_name']);

            $table->timestamps();
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_account');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
