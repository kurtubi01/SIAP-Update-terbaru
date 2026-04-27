<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'tb_notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = true;
    public const UPDATED_AT = null;

    protected $fillable = [
        'id_sop',
        'id_user',
        'pesan',
        'judul',
        'tipe',
        'status_baca',
        'status_tindak_lanjut',
        'metadata',
        'dibaca_pada',
        'ditindaklanjuti_pada',
        'ditindaklanjuti_oleh',
    ];

    protected function casts(): array
    {
        return [
            'status_tindak_lanjut' => 'boolean',
            'metadata' => 'array',
            'dibaca_pada' => 'datetime',
            'ditindaklanjuti_pada' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'ditindaklanjuti_oleh', 'id_user');
    }

    public function sop()
    {
        return $this->belongsTo(Sop::class, 'id_sop', 'id_sop');
    }
}
