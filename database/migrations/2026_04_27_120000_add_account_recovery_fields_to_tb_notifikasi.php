<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_notifikasi', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_notifikasi', 'id_user')) {
                $table->integer('id_user')->nullable()->after('id_sop');
            }

            if (!Schema::hasColumn('tb_notifikasi', 'judul')) {
                $table->string('judul', 255)->nullable()->after('pesan');
            }

            if (!Schema::hasColumn('tb_notifikasi', 'tipe')) {
                $table->string('tipe', 100)->nullable()->after('judul');
            }

            if (!Schema::hasColumn('tb_notifikasi', 'status_tindak_lanjut')) {
                $table->boolean('status_tindak_lanjut')->default(0)->after('status_baca');
            }

            if (!Schema::hasColumn('tb_notifikasi', 'metadata')) {
                $table->text('metadata')->nullable()->after('status_tindak_lanjut');
            }

            if (!Schema::hasColumn('tb_notifikasi', 'dibaca_pada')) {
                $table->dateTime('dibaca_pada')->nullable()->after('metadata');
            }

            if (!Schema::hasColumn('tb_notifikasi', 'ditindaklanjuti_pada')) {
                $table->dateTime('ditindaklanjuti_pada')->nullable()->after('dibaca_pada');
            }

            if (!Schema::hasColumn('tb_notifikasi', 'ditindaklanjuti_oleh')) {
                $table->integer('ditindaklanjuti_oleh')->nullable()->after('ditindaklanjuti_pada');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_notifikasi', function (Blueprint $table) {
            foreach ([
                'ditindaklanjuti_oleh',
                'ditindaklanjuti_pada',
                'dibaca_pada',
                'metadata',
                'status_tindak_lanjut',
                'tipe',
                'judul',
                'id_user',
            ] as $column) {
                if (Schema::hasColumn('tb_notifikasi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
