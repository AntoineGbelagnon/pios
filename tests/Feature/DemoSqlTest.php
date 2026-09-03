<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DemoSqlTest extends TestCase
{
    public function test_demo_sql_is_compatible_with_the_current_database_schema(): void
    {
        $sql = file_get_contents(base_path('donnees_demo.sql'));
        $sql = preg_replace('/\bCOMMIT;/', 'ROLLBACK;', $sql, 1);

        $this->assertIsString($sql);
        $this->assertTrue(DB::unprepared($sql));
    }
}
