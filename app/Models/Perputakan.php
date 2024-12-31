<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perputakan extends Model
{
    use HasFactory;
    protected $table = 'perputakan';

    protected $fillable = [
        'nama', 'nis', 'kelas', 'keterangan', 'status_masuk', 'status_keluar', 'durasi'
    ];
}
