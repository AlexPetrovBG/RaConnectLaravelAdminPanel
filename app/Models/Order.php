<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = [
        'number',
        'description',
        'install_date',
        'install_date_confirmed',
        'price_to_customer',
        'price_to_supplier',
        'budget',
        'montage_time',
        'is_requested',
        'is_confirmed',
        'is_delivered',
        'is_finished',
        'user_id',
        'client_id',
        'place_id',
        'project_id',
        'order_category_id',
    ];

    protected $casts = [
        'install_date' => 'date',
        'install_date_confirmed' => 'date',
        'price_to_customer' => 'decimal:2',
        'price_to_supplier' => 'decimal:2',
        'budget' => 'decimal:2',
        'is_requested' => 'boolean',
        'is_confirmed' => 'boolean',
        'is_delivered' => 'boolean',
        'is_finished' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function orderCategory(): BelongsTo
    {
        return $this->belongsTo(OrderCategory::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function montages(): HasMany
    {
        return $this->hasMany(Montage::class);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_order')
            ->withPivot(['quantity', 'price', 'is_requested', 'is_confirmed', 'is_delivered'])
            ->withTimestamps();
    }
}
