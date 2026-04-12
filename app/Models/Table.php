<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'table_number',
        'capacity',
        'qr_token',
        'qr_code_path',
        'status',
        'is_active',
    ];

    /**
     * The "booted" method of the model.
     * Jab bhi nayi Table save hogi, ye automatic token generate karega.
     */
    protected static function booted()
    {
        static::creating(function ($table) {
            // Generate a unique 10-character random token
            // Example: RT-X89K2-Z1
            $table->qr_token = 'RT-' . strtoupper(Str::random(6)) . '-' . rand(10, 99);

            // Default status set karna agar bhool gaye ho toh
            if (!$table->status) {
                $table->status = 'available';
            }
        });
    }

    // --- Relationships ---

    // Har table ek Tenant (Owner) ki hoti hai
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Har table ek specific Branch ki hoti hai
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Table ke saare active orders dekhne ke liye
    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    /**
     * Helper: Table ka live QR Link nikalne ke liye
     */
    public function getQrLinkAttribute()
    {
        // Ye wahi link hai jo customer scan karega
        return url('/menu/' . $this->qr_token);
    }
}
