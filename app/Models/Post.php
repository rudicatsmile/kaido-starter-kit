<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Post extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'content',
        'author',
        'published_at',
        'status',
        'slug',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            $model->slug = $model->slug ?: static::generateUniqueSlug($model->title);
            $model->status = $model->status ?: 'draft';
        });

        static::updating(function (self $model): void {
            if ($model->isDirty('title') && ! $model->isDirty('slug')) {
                $model->slug = static::generateUniqueSlug($model->title, $model->getKey());
            }
        });
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while (static::query()
            ->when($ignoreId, fn($q) => $q->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('post')
            ->logFillable()
            ->logOnlyDirty();
    }
}
