<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RendicontoEmailLog extends Model
{
    use HasFactory;

    protected $table = 'rendiconto_email_logs';

    protected $fillable = [
        'magazzino_id',
        'email',
        'data_invio',
    ];

    public $timestamps = true;
}
