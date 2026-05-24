<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PrevDogTitle extends Pivot
{
    use LogsActivity;
    use SoftDeletes;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Dogs_ScoresDB';

    // Disable Fillable Attributes
    protected $guarded = [];

    // casting the attributes to the correct types
    protected $casts = [
        'SagirID' => 'integer',
        'AwardID' => 'integer',
        'ShowID' => 'integer',
        'EventDate' => 'date',
    ];

    public function title(): BelongsTo
    {
        return $this->belongsTo(PrevTitle::class, 'AwardID', 'TitleCode');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID', 'SagirID');
    }

    public function show(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID', 'id');
    }

    // get title name using AwardID
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->relationLoaded('title')
                ? $this->title?->name
                : null,
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()->logOnlyDirty();
    }
}
