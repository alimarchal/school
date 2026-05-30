<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $appPermissions = [
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.assign-role',
            'user.assign-permission',
        ];
        $accountingPermissions = collect(config('accounting.permissions', []))
            ->mapWithKeys(fn (string $permission): array => [$permission => $user?->can($permission) ?? false])
            ->all();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
                'can' => collect($appPermissions)
                    ->mapWithKeys(fn (string $permission): array => [
                        str($permission)->replace('.', '_')->camel()->toString() => $user?->can($permission) ?? false,
                    ])
                    ->all(),
                'accountingPermissions' => $accountingPermissions,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
