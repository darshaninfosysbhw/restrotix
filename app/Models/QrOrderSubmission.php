<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrOrderSubmission extends Model
{
    protected $guarded = ['id'];

    protected $attributes = ['status' => 'pending'];

    protected $casts = ['payload' => 'array', 'total' => 'decimal:2', 'handled_at' => 'datetime'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function accessSession()
    {
        return $this->belongsTo(TableAccessSession::class, 'table_access_session_id');
    }
}
