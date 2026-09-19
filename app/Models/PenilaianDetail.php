<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['penilaian_id', 'item_id', 'jawaban'])]
class PenilaianDetail extends Model
{
    protected $table = 'penilaian_detail';

    protected function casts(): array
    {
        return ['jawaban' => 'boolean'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemPerkembangan::class, 'item_id');
    }
}
