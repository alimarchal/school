import { Head, Link, router, usePage } from '@inertiajs/react';
import { Edit, Filter, Plus, Trash2, X } from 'lucide-react';
import type { FormEvent } from 'react';
import { useEffect, useMemo, useState } from 'react';
import { SearchableSelect } from '@/components/accounting/searchable-select';
import Heading from '@/components/heading';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { playErrorSound, playSuccessSound } from '@/lib/sounds';
import type { BreadcrumbItem } from '@/types';

type UserRow = {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    roles: Array<{ name: string }>;
    permissions: Array<{ name: string }>;
};

type Paginator<T> = {
    data: T[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    from: number | null;
    to: number | null;
    total: number;
};

type Filters = {
    name?: string | null;
    email?: string | null;
    role?: string | null;
    permission?: string | null;
    status?: string | null;
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Users', href: '/users' }];

export default function UsersIndex({ users, filters, roles, permissions }: { users: Paginator<UserRow>; filters: Filters; roles: string[]; permissions: string[] }) {
    const { auth, flash } = usePage().props;
    const [filtersOpen, setFiltersOpen] = useState(false);
    const [values, setValues] = useState({
        name: filters.name ?? '',
        email: filters.email ?? '',
        role: filters.role ?? '',
        permission: filters.permission ?? '',
        status: filters.status ?? '',
    });

    const roleOptions = useMemo(() => roles.map((role) => ({ value: role, label: role })), [roles]);
    const permissionOptions = useMemo(() => permissions.map((permission) => ({ value: permission, label: permission })), [permissions]);
    const hasFilters = Object.values(filters).some((value) => value);
    const activeFilterCount = Object.values(values).filter(Boolean).length;

    useEffect(() => {
        if (flash.success) {
            playSuccessSound();
        }
    }, [flash.success]);

    useEffect(() => {
        if (flash.error) {
            playErrorSound();
        }
    }, [flash.error]);

    const applyFilters = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        router.get(
            '/users',
            {
                filter: {
                    name: values.name || undefined,
                    email: values.email || undefined,
                    'roles.name': values.role || undefined,
                    'permissions.name': values.permission || undefined,
                    status: values.status || undefined,
                },
            },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    };

    const resetFilters = () => {
        setValues({ name: '', email: '', role: '', permission: '', status: '' });
        router.get('/users', {}, { preserveState: true, preserveScroll: true, replace: true });
    };

    const deleteUser = (user: UserRow) => {
        if (window.confirm(`Delete ${user.name}?`)) {
            router.delete(`/users/${user.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Users" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex flex-col justify-between gap-3 md:flex-row md:items-center">
                    <Heading title="User management" description="Manage roles and direct permission exceptions using Spatie permissions." />
                    <div className="flex flex-wrap gap-2">
                        <Button
                            type="button"
                            variant={filtersOpen ? 'secondary' : 'outline'}
                            className="relative gap-2"
                            title="Filters"
                            aria-label="Filters"
                            onClick={() => setFiltersOpen((open) => !open)}
                        >
                            <Filter className="size-4" />
                            <span>Filters</span>
                            {hasFilters ? <span className="absolute -right-1 -top-1 size-2 rounded-full bg-primary" /> : null}
                        </Button>
                        {auth.can?.userCreate ? (
                            <Button asChild className="gap-2" title="New user" aria-label="New user">
                                <Link href="/users/create" aria-label="New user">
                                    <Plus className="size-4" />
                                    <span>New user</span>
                                </Link>
                            </Button>
                        ) : null}
                    </div>
                </div>

                {flash.success ? (
                    <Alert className="border-green-500/30 bg-green-500/5">
                        <AlertTitle>Success</AlertTitle>
                        <AlertDescription>{flash.success}</AlertDescription>
                    </Alert>
                ) : null}
                {flash.error ? (
                    <Alert variant="destructive">
                        <AlertTitle>Error</AlertTitle>
                        <AlertDescription>{flash.error}</AlertDescription>
                    </Alert>
                ) : null}

                {filtersOpen ? (
                    <Card className="rounded-lg py-4">
                        <CardHeader className="px-4 pb-3">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <CardTitle className="text-sm">Filters</CardTitle>
                                    {activeFilterCount > 0 ? <Badge variant="secondary">{activeFilterCount} active</Badge> : null}
                                </div>
                                <Button type="button" variant="ghost" size="icon" className="size-8" onClick={() => setFiltersOpen(false)}>
                                    <X className="size-4" />
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent className="px-4">
                            <form onSubmit={applyFilters} className="space-y-4">
                                <div className="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                                    <div className="grid gap-2">
                                        <Label htmlFor="filter-name">Name</Label>
                                        <Input id="filter-name" value={values.name} onChange={(event) => setValues({ ...values, name: event.target.value })} />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="filter-email">Email</Label>
                                        <Input id="filter-email" value={values.email} onChange={(event) => setValues({ ...values, email: event.target.value })} />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label>Role</Label>
                                        <SearchableSelect
                                            key={`role-${values.role || 'all'}`}
                                            value={values.role}
                                            options={roleOptions}
                                            placeholder="All roles"
                                            onChange={(role) => setValues({ ...values, role })}
                                        />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label>Permission</Label>
                                        <SearchableSelect
                                            key={`permission-${values.permission || 'all'}`}
                                            value={values.permission}
                                            options={permissionOptions}
                                            placeholder="All permissions"
                                            onChange={(permission) => setValues({ ...values, permission })}
                                        />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="filter-status">Status</Label>
                                        <Select value={values.status || 'all'} onValueChange={(status) => setValues({ ...values, status: status === 'all' ? '' : status })}>
                                            <SelectTrigger id="filter-status" className="w-full">
                                                <SelectValue placeholder="All" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="all">All</SelectItem>
                                                <SelectItem value="verified">Verified</SelectItem>
                                                <SelectItem value="unverified">Unverified</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>
                                <div className="flex justify-end gap-2">
                                    <Button type="submit" className="min-w-20">Apply</Button>
                                    <Button type="button" variant="outline" onClick={resetFilters}>Clear</Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                ) : null}

                <div className="overflow-hidden rounded-lg border">
                    <table className="w-full min-w-[920px] text-sm">
                        <thead className="bg-muted/50 text-left">
                            <tr>
                                <th className="p-3 font-medium">User</th>
                                <th className="p-3 font-medium">Roles</th>
                                <th className="p-3 font-medium">Direct Permissions</th>
                                <th className="p-3 font-medium">Status</th>
                                <th className="w-32 p-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {users.data.map((user) => (
                                <tr key={user.id} className="border-t">
                                    <td className="p-3">
                                        <div className="font-medium">{user.name}</div>
                                        <div className="text-muted-foreground">{user.email}</div>
                                    </td>
                                    <td className="p-3">
                                        <div className="flex flex-wrap gap-1">
                                            {user.roles.map((role) => <Badge key={role.name} variant="secondary">{role.name}</Badge>)}
                                        </div>
                                    </td>
                                    <td className="p-3">
                                        <div className="flex flex-wrap gap-1">
                                            {user.permissions.length ? user.permissions.map((permission) => <Badge key={permission.name} variant="outline">{permission.name}</Badge>) : <span className="text-muted-foreground">None</span>}
                                        </div>
                                    </td>
                                    <td className="p-3">{user.email_verified_at ? 'Verified' : 'Unverified'}</td>
                                    <td className="p-3">
                                        <div className="flex justify-end gap-1">
                                            {auth.can?.userUpdate ? (
                                                <Button asChild size="icon" variant="ghost" title="Edit">
                                                    <Link href={`/users/${user.id}/edit`}><Edit className="size-4" /></Link>
                                                </Button>
                                            ) : null}
                                            {auth.can?.userDelete && auth.user.id !== user.id ? (
                                                <Button size="icon" variant="ghost" title="Delete" onClick={() => deleteUser(user)}>
                                                    <Trash2 className="size-4" />
                                                </Button>
                                            ) : null}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="flex flex-wrap items-center justify-between gap-3 text-sm text-muted-foreground">
                    <div>{users.from && users.to ? `Showing ${users.from}-${users.to} of ${users.total}` : `${users.total} users`}</div>
                    <div className="flex flex-wrap gap-1">
                        {users.links.map((link, index) => (
                            <Button key={index} asChild={Boolean(link.url)} disabled={!link.url} variant={link.active ? 'secondary' : 'outline'} size="sm">
                                {link.url ? <Link href={link.url} dangerouslySetInnerHTML={{ __html: link.label }} /> : <span dangerouslySetInnerHTML={{ __html: link.label }} />}
                            </Button>
                        ))}
                    </div>
                </div>
            </div>
        </>
    );
}

UsersIndex.layout = {
    breadcrumbs,
};
