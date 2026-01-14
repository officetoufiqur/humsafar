<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'dashboard.view','dashboard.create','dashboard.edit','dashboard.delete',
            'members.view','members.create','members.edit','members.delete',
            'profile_attributes.view','profile_attributes.create','profile_attributes.edit','profile_attributes.delete',
            'payments.view','payments.create','payments.edit','payments.delete',
            'frontend_settings.view','frontend_settings.create','frontend_settings.edit','frontend_settings.delete',
            'faqs.view','faqs.create','faqs.edit','faqs.delete',
            'blogs.view','blogs.create','blogs.edit','blogs.delete',
            'packages.view','packages.create','packages.edit','packages.delete',
            'complaints.view','complaints.create','complaints.edit','complaints.delete',
            'reports.view','reports.create','reports.edit','reports.delete',
            'marketing.view','marketing.create','marketing.edit','marketing.delete',
            'settings.view','settings.create','settings.edit','settings.delete',
            'chat_settings.view','chat_settings.create','chat_settings.edit','chat_settings.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
