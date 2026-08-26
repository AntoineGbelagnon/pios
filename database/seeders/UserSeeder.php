<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Country;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::where('iso_code', 'TG')->firstOrFail();
        $company = Company::updateOrCreate(
            ['name' => 'PIOS Demo'],
            ['legal_name' => 'PIOS Demo SARL', 'country_id' => $country->id, 'currency_id' => $country->currency_id, 'is_active' => true]
        );
        $shop = Shop::updateOrCreate(
            ['company_id' => $company->id, 'code' => 'LOM-001'],
            ['name' => 'Boutique principale', 'city' => 'Lomé', 'is_active' => true]
        );
        $user = User::updateOrCreate(
            ['email' => config('auth.two_factor.default_email', 'celinbell195@gmail.com')],
            ['name' => 'Administrateur PIOS', 'company_id' => $company->id, 'password' => Hash::make('password'), 'is_active' => true]
        );

        $shop->users()->syncWithoutDetaching([$user->id => ['is_default' => true]]);
        app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);
        $user->assignRole('admin');
    }
}
