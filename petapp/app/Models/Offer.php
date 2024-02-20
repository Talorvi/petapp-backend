<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Offer extends Model implements HasMedia
{
    use HasFactory, Searchable, HasUuids, InteractsWithMedia;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $appends = ['average_rating', 'user', 'images'];

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

    protected $hidden = [
        'media'
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('offer_images')->useDisk('public');
    }

    public function images(): MorphMany
    {
        return $this->media()->where('collection_name', 'offer_images');
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

    public function getImagesAttribute()
    {
        // Use the getMedia method from Spatie's package to fetch the 'offer_images' collection
        return $this->getMedia('offer_images')->map(function ($item) {
            // For each media item, return its URL
            return $item->getUrl();
        })->toArray();
    }

}
