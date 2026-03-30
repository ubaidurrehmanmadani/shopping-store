<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;

class Currency
{
    private static ?string $currentCode = null;

    /**
     * @return array<string, array{name: string, symbol: string, flag: string}>
     */
    public static function all(): array
    {
        /** @var array<string, array{name: string, symbol: string, flag: string}> $currencies */
        $currencies = config('currencies', []);

        return $currencies;
    }

    /**
     * @return array{name: string, symbol: string, flag: string}
     */
    public static function details(?string $code): array
    {
        $code = strtoupper((string) ($code ?: static::currentCode()));

        return static::all()[$code] ?? static::all()['USD'];
    }

    public static function format(float|int|string|null $amount, ?string $code): string
    {
        $details = static::details($code);

        return sprintf('%s%s', $details['symbol'], number_format((float) $amount, 2));
    }

    public static function currentCode(): string
    {
        if (static::$currentCode !== null) {
            return static::$currentCode;
        }

        if (! Schema::hasTable('app_settings')) {
            static::$currentCode = 'USD';

            return static::$currentCode;
        }

        static::$currentCode = strtoupper((string) (AppSetting::pairs()->get('site_currency') ?: 'USD'));

        if (! array_key_exists(static::$currentCode, static::all())) {
            static::$currentCode = 'USD';
        }

        return static::$currentCode;
    }

    public static function flush(): void
    {
        static::$currentCode = null;
    }
}
