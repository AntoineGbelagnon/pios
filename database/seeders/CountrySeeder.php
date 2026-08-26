<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $xof = Currency::where('code', 'XOF')->firstOrFail();
        $ghs = Currency::where('code', 'GHS')->firstOrFail();

        foreach ([
            ['name' => 'Togo', 'iso_code' => 'TG', 'currency_id' => $xof->id, 'phone_prefix' => '+228', 'payment_providers' => ['tmoney', 'flooz']],
            ['name' => 'Bénin', 'iso_code' => 'BJ', 'currency_id' => $xof->id, 'phone_prefix' => '+229', 'payment_providers' => ['mtn_momo', 'moov_money']],
            ['name' => 'Côte d’Ivoire', 'iso_code' => 'CI', 'currency_id' => $xof->id, 'phone_prefix' => '+225', 'payment_providers' => ['orange_money', 'mtn_momo', 'moov_money']],
            ['name' => 'Ghana', 'iso_code' => 'GH', 'currency_id' => $ghs->id, 'phone_prefix' => '+233', 'payment_providers' => ['mtn_momo', 'vodafone_cash']],
            ['name' => 'Sénégal', 'iso_code' => 'SN', 'currency_id' => $xof->id, 'phone_prefix' => '+221', 'payment_providers' => ['wave', 'orange_money']],
        ] as $country) {
            Country::updateOrCreate(['iso_code' => $country['iso_code']], $country + ['tax_rules' => []]);
        }
    }
}
