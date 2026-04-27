<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE tb_notifikasi MODIFY id_sop INT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tb_notifikasi MODIFY id_sop INT NOT NULL');
    }
};
