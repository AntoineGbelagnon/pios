<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $newEmail = config('auth.two_factor.default_email', 'celinbell195@gmail.com');

        if (! DB::table('users')->where('email', $newEmail)->exists()) {
            DB::table('users')
                ->where('email', 'admin@pios.test')
                ->update(['email' => $newEmail, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        $newEmail = config('auth.two_factor.default_email', 'celinbell195@gmail.com');

        if (! DB::table('users')->where('email', 'admin@pios.test')->exists()) {
            DB::table('users')
                ->where('email', $newEmail)
                ->where('name', 'Administrateur PIOS')
                ->update(['email' => 'admin@pios.test', 'updated_at' => now()]);
        }
    }
};
