<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tb_login_logs')) {
            Schema::create('tb_login_logs', function (Blueprint $table) {
                $table->bigIncrements('id_login_log');
                $table->unsignedInteger('id_user');
                $table->string('session_id', 255)->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->dateTime('login_at');
                $table->dateTime('last_activity_at')->nullable();
                $table->dateTime('logout_at')->nullable();
                $table->boolean('is_active')->default(true);
            });
        }

        Schema::table('tb_user', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_user', 'current_session_id')) {
                $table->string('current_session_id', 255)->nullable();
            }
            if (!Schema::hasColumn('tb_user', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable();
            }
            if (!Schema::hasColumn('tb_user', 'last_login_at')) {
                $table->dateTime('last_login_at')->nullable();
            }
            if (!Schema::hasColumn('tb_user', 'last_activity_at')) {
                $table->dateTime('last_activity_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('tb_user')) {
            Schema::table('tb_user', function (Blueprint $table) {
                $droppable = [];

                foreach (['current_session_id', 'last_login_ip', 'last_login_at', 'last_activity_at'] as $column) {
                    if (Schema::hasColumn('tb_user', $column)) {
                        $droppable[] = $column;
                    }
                }

                if (!empty($droppable)) {
                    $table->dropColumn($droppable);
                }
            });
        }

        Schema::dropIfExists('tb_login_logs');
    }
};
