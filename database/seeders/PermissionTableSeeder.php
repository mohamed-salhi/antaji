<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'admin',
            'product',
            'location',
            'service',
            'support',
            'payment',
            'conversation',
            'notification',
            'place',
            'order',
            'ads',
            'intro',
            'user',
            'artisan',
            'job',
            'course',
            'chat',
            'discount',
            'package',
            'setting',
            'social',
            'document',
            'contact-us'

        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
