<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PrevDogDocument extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'dogs_documents';

    protected $guarded = [];

    /**
     * Attribute casts.
     */
    protected function casts(): array
    {
        return [
            'SagirID' => 'integer',
            'is_maag' => 'boolean',
            'result' => 'integer',
        ];
    }

    /**
     * The dog this document belongs to.
     */
    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID', 'SagirID');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()->logOnlyDirty();
    }
}
