<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'XOF', 'name' => 'Franc CFA BCEAO', 'symbol' => 'F CFA', 'decimal_places' => 0],
            ['code' => 'GHS', 'name' => 'Ghanaian Cedi', 'symbol' => 'GH₵', 'decimal_places' => 2],
        ] as $currency) {
            Currency::updateOrCreate(['code' => $currency['code']], $currency);
        }
    }
}
