<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use App\Support\Countries;
use Illuminate\Database\Eloquent\Model;

class VisitedCountry extends Model
{
    use Orderable;

    protected $fillable = ['code', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getNameAttribute(): string
    {
        return Countries::ALL[$this->code] ?? strtoupper($this->code);
    }
}
