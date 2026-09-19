<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['aspek', 'kelompok_usia', 'pertanyaan', 'urutan'])]
class ItemPerkembangan extends Model
{
    protected $table = 'item_perkembangan';
}
