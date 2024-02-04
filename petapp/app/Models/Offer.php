<?php

namespace App\Models;

use App\Traits\ConditionallySearchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Offer extends Model
{
    use HasFactory, Searchable;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $appends = ['average_rating', 'user'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'price',
        'end_date',
        'user_id',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        // Retrieve the average rating from the database or cache
        $averageRating = $this->averageRating();

        // Return the array of attributes you want to index
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => (float) $this->price,
            'updated_at' => $this->updated_at->getTimestamp(),
            'average_rating' => (float) $averageRating,
            'user_id' => $this->user_id
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    // Accessor for the average rating
    public function getAverageRatingAttribute()
    {
        return $this->averageRating();
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function getUserAttribute()
    {
        return $this->user()->first(); // Use the existing user relationship
    }
}
