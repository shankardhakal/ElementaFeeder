<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Traits\Encryptable; // Use our custom trait
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Website extends Model
{
    use CrudTrait;
    use HasFactory;
    use Encryptable; // This now correctly points to our trait

    protected $fillable = [
        'name',
        'url',
        'destination_platform_id',
        'credentials',
        'rate_limit_per_minute',
        'is_active',
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_active' => 'boolean',
    ];

    // This array tells our custom trait which fields to encrypt
    protected $encryptable = [
        'credentials',
    ];

    public function destinationPlatform(): BelongsTo
    {
        return $this->belongsTo(DestinationPlatform::class);
    }
    
    public function mappingRules(): MorphMany
    {
        return $this->morphMany(MappingRule::class, 'mappable');
    }
}