<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_monitoring', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_monitoring', 'prosedur')) {
                $table->text('prosedur')->nullable()->after('tanggal');
            }

            if (!Schema::hasColumn('tb_monitoring', 'tindakan')) {
                $table->text('tindakan')->nullable()->after('hasil_monitoring');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_monitoring', function (Blueprint $table) {
            if (Schema::hasColumn('tb_monitoring', 'prosedur')) {
                $table->dropColumn('prosedur');
            }

            if (Schema::hasColumn('tb_monitoring', 'tindakan')) {
                $table->dropColumn('tindakan');
            }
        });
    }
};
