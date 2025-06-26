<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Feed extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'name',
        'feed_url',
        'product_type_id',
        'parser_type',
        'parser_options',
        'unique_identifier_field',
        'is_active',
        'schedule_cron',
        'last_import_status',
    ];

    protected $casts = [
        'parser_options' => 'array',
        'is_active' => 'boolean',
    ];

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }
  public function network()
{
    return $this->belongsTo(Network::class);
}

    public function websites(): BelongsToMany
    {
        return $this->belongsToMany(Website::class, 'feed_website')->withPivot('is_active');
    }

    public function transformationRules(): HasMany
    {
        return $this->hasMany(TransformationRule::class);
    }

    public function mappingRules(): MorphMany
    {
        return $this->morphMany(MappingRule::class, 'mappable');
    }
}