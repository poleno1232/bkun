<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RoleEnum::cases() as $case) {
            /** @var Role $role */
            $role = Role::updateOrCreate([
                'name' => $case->value,
                'guard_name' => config('auth.defaults.guard')
            ]);
        }
    }
}
