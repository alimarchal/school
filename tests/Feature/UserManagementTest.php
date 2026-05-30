<?php

use App\Accounting\Database\Seeders\AccountingDatabaseSeeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function (): void {
    $this->withoutVite();
    $this->seed(AccountingDatabaseSeeder::class);
});

it('blocks user management after the permission is removed', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('user.view');

    $this->actingAs($user)
        ->get('/users')
        ->assertSuccessful();

    $user->revokePermissionTo('user.view');
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $this->actingAs($user->fresh())
        ->get('/users')
        ->assertForbidden();
});

it('syncs roles and direct permissions from the user edit screen', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo([
        'user.view',
        'user.update',
        'user.assign-role',
        'user.assign-permission',
    ]);

    $target = User::factory()->create();
    $target->assignRole('viewer');
    $target->givePermissionTo('journal-entries.post');

    $this->actingAs($admin)
        ->put("/users/{$target->id}", [
            'name' => $target->name,
            'email' => $target->email,
            'password' => null,
            'password_confirmation' => null,
            'roles' => ['accountant'],
            'permissions' => ['journal-entries.reverse'],
        ])
        ->assertRedirect('/users');

    $target->refresh();

    expect($target->hasRole('viewer'))->toBeFalse()
        ->and($target->hasRole('accountant'))->toBeTrue()
        ->and($target->hasDirectPermission('journal-entries.post'))->toBeFalse()
        ->and($target->hasDirectPermission('journal-entries.reverse'))->toBeTrue();
});

it('prevents deleting your own user account from user management', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('user.delete');

    $this->actingAs($user)
        ->delete("/users/{$user->id}")
        ->assertForbidden();

    expect(User::query()->whereKey($user->id)->exists())->toBeTrue();
});

it('gives super admin all user management abilities through the gate', function (): void {
    $user = User::factory()->create();
    $user->assignRole(Role::findByName('super-admin'));

    expect($user->can('user.view'))->toBeTrue()
        ->and($user->can('user.update'))->toBeTrue()
        ->and($user->can('reports.trial-balance.view'))->toBeTrue();
});
