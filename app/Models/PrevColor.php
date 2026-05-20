<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PrevColor extends Model
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
    protected $table = 'ColorsDB';

    public $timestamps = true;

    // Disable Fillable Attributes
    protected $guarded = [];

    protected $casts = [
        'OldCode' => 'integer',
    ];

    public function dogs(): HasMany
    {
        return $this->hasMany(PrevDog::class, 'ColorID', 'OldCode');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()->logOnlyDirty();
    }
}
