<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableQrScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'table_id',
        'table_access_session_id',
        'qr_token',
        'session_token',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'branch_id' => 'integer',
        'table_id' => 'integer',
        'table_access_session_id' => 'integer',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
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
