<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:user.view', only: ['index', 'show']),
            new Middleware('permission:user.create', only: ['create', 'store']),
            new Middleware('permission:user.update', only: ['edit', 'update']),
            new Middleware('permission:user.delete', only: ['destroy']),
            new Middleware('permission:user.assign-role', only: ['create', 'store', 'edit', 'update']),
            new Middleware('permission:user.assign-permission', only: ['create', 'store', 'edit', 'update']),
        ];
    }

    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        $users = QueryBuilder::for(User::query()->with(['roles:id,name', 'permissions:id,name']))
            ->allowedFilters(...[
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::exact('roles.name', 'roles.name'),
                AllowedFilter::exact('permissions.name', 'permissions.name'),
                AllowedFilter::callback('status', function (Builder $query, mixed $value): void {
                    if ($value === 'verified') {
                        $query->whereNotNull('email_verified_at');
                    }

                    if ($value === 'unverified') {
                        $query->whereNull('email_verified_at');
                    }
                }),
            ])
            ->allowedSorts(...['id', 'name', 'email', 'created_at'])
            ->defaultSort('-created_at')
            ->paginate(15)
            ->withQueryString();

        $activeFilters = (array) request()->input('filter', []);

        return Inertia::render('users/index', [
            'users' => $users,
            'filters' => [
                'name' => $activeFilters['name'] ?? null,
                'email' => $activeFilters['email'] ?? null,
                'role' => $activeFilters['roles.name'] ?? null,
                'permission' => $activeFilters['permissions.name'] ?? null,
                'status' => $activeFilters['status'] ?? null,
            ],
            'roles' => Role::query()->orderBy('name')->pluck('name'),
            'permissions' => Permission::query()->orderBy('name')->pluck('name'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('users/create', $this->formOptions());
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated): void {
                $user = User::query()->create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                ]);

                $user->syncRoles($validated['roles']);
                $user->syncPermissions($validated['permissions'] ?? []);
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return to_route('users.index')->with('success', 'User created successfully.');
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->withErrors([
                'users' => 'Unable to create user right now. Please try again.',
            ]);
        }
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        return Inertia::render('users/edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->values()->all(),
                'permissions' => $user->getDirectPermissions()->pluck('name')->values()->all(),
            ],
            ...$this->formOptions(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validated();

        try {
            DB::transaction(function () use ($user, $validated): void {
                $user->fill([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);

                if (! empty($validated['password'])) {
                    $user->password = $validated['password'];
                }

                $user->save();
                $user->syncRoles($validated['roles']);
                $user->syncPermissions($validated['permissions'] ?? []);
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return to_route('users.index')->with('success', 'User updated successfully.');
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->withErrors([
                'users' => 'Unable to update user right now. Please try again.',
            ]);
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        try {
            DB::transaction(fn (): ?bool => $user->delete());

            return to_route('users.index')->with('success', 'User deleted successfully.');
        } catch (Throwable $exception) {
            report($exception);

            return to_route('users.index')->with('error', 'Unable to delete user right now.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'roles' => Role::query()->orderBy('name')->pluck('name'),
            'permissions' => Permission::query()->orderBy('name')->pluck('name'),
        ];
    }
}
