<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDetail extends Model
{
    use HasFactory;

    // Fillable fields migration ke hisaab se
    protected $fillable = [
        'user_id',
        'employee_id',
        'designation',
        'pin_code',
        'id_type',
        'id_number',
        'id_proof_path',
        'date_of_birth',
        'gender',
        'blood_group',
        'current_address',
        'permanent_address',
        'emergency_contact_name',
        'emergency_contact_number',
        'joining_date',
        'exit_date',
        'base_salary',
        'bank_name',
        'account_number',
        'ifsc_code',
    ];

    // Dates ko Carbon instance mein badalne ke liye
    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'exit_date' => 'date',
        'base_salary' => 'decimal:2',
    ];

    /**
     * Relationship: Detail hamesha ek User ko belong karta hai.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
