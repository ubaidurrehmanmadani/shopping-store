<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

#[Fillable(['key', 'value'])]
class AppSetting extends Model
{
    public $timestamps = false;

    public static function pairs(): Collection
    {
        return static::query()
            ->orderBy('key')
            ->get()
            ->mapWithKeys(fn (self $setting): array => [$setting->key => $setting->value]);
    }

    /**
     * @param  array<string, string|null>  $values
     */
    public static function storeMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
