<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tb_activity_logs')) {
            Schema::create('tb_activity_logs', function (Blueprint $table) {
                $table->bigIncrements('id_activity_log');
                $table->unsignedInteger('id_user')->nullable()->index();
                $table->dateTime('activity_time')->index();
                $table->string('activity', 150);
                $table->text('detail')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('device', 160)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('route_name', 150)->nullable();
                $table->string('http_method', 10)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_activity_logs');
    }
};
