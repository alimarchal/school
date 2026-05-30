import { Head, Link, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useMemo, useState } from 'react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type EditableUser = {
    id: number;
    name: string;
    email: string;
    roles: string[];
    permissions: string[];
};

function toggleValue(items: string[], value: string, checked: boolean): string[] {
    return checked ? [...items, value] : items.filter((item) => item !== value);
}

export default function EditUser({ user, roles, permissions }: { user: EditableUser; roles: string[]; permissions: string[] }) {
    const { auth, flash } = usePage().props;
    const [roleSearch, setRoleSearch] = useState('');
    const [permissionSearch, setPermissionSearch] = useState('');
    const form = useForm({
        name: user.name,
        email: user.email,
        password: '',
        password_confirmation: '',
        roles: user.roles,
        permissions: user.permissions,
    });
    const formErrors = form.errors as Record<string, string | undefined>;
    const visibleRoles = useMemo(() => roles.filter((role) => role.toLowerCase().includes(roleSearch.toLowerCase())), [roles, roleSearch]);
    const visiblePermissions = useMemo(
        () => permissions.filter((permission) => permission.toLowerCase().includes(permissionSearch.toLowerCase())),
        [permissions, permissionSearch],
    );

    const submit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        form.put(`/users/${user.id}`);
    };

    return (
        <>
            <Head title={`Edit ${user.name}`} />
            <div className="space-y-6 p-4">
                <div className="flex flex-col justify-between gap-3 md:flex-row md:items-center">
                    <Heading title={`Edit ${user.name}`} description="Role changes and direct permission removals are applied through Spatie sync methods." />
                    <Button asChild variant="outline"><Link href="/users">Back</Link></Button>
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

                <form onSubmit={submit} className="grid gap-6 lg:grid-cols-3">
                    <Card className="rounded-lg lg:col-span-2">
                        <CardHeader>
                            <CardTitle>Profile</CardTitle>
                            <CardDescription>Leave password blank to keep the existing password.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input id="name" value={form.data.name} onChange={(event) => form.setData('name', event.target.value)} />
                                <InputError message={form.errors.name} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="email">Email</Label>
                                <Input id="email" type="email" value={form.data.email} onChange={(event) => form.setData('email', event.target.value)} />
                                <InputError message={form.errors.email} />
                            </div>
                            <div className="grid gap-2 md:grid-cols-2">
                                <div className="grid gap-2">
                                    <Label htmlFor="password">New password</Label>
                                    <Input id="password" type="password" value={form.data.password} onChange={(event) => form.setData('password', event.target.value)} />
                                    <InputError message={form.errors.password} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="password_confirmation">Confirm new password</Label>
                                    <Input id="password_confirmation" type="password" value={form.data.password_confirmation} onChange={(event) => form.setData('password_confirmation', event.target.value)} />
                                    <InputError message={form.errors.password_confirmation} />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="rounded-lg">
                        <CardHeader>
                            <CardTitle>Roles</CardTitle>
                            <CardDescription>Use roles as the normal access model.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <Input value={roleSearch} onChange={(event) => setRoleSearch(event.target.value)} placeholder="Search roles" />
                            <div className="max-h-72 space-y-2 overflow-auto pr-1">
                                {visibleRoles.map((role) => (
                                    <label key={role} className="flex items-center gap-2 text-sm">
                                        <Checkbox checked={form.data.roles.includes(role)} onCheckedChange={(checked) => form.setData('roles', toggleValue(form.data.roles, role, Boolean(checked)))} />
                                        <span>{role}</span>
                                    </label>
                                ))}
                            </div>
                            <InputError message={form.errors.roles} />
                        </CardContent>
                    </Card>

                    {auth.can?.userAssignPermission ? (
                        <Card className="rounded-lg lg:col-span-3">
                            <CardHeader>
                                <CardTitle>Direct permissions</CardTitle>
                                <CardDescription>Use only for user-specific exceptions. Unchecking removes the direct permission.</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                <Input value={permissionSearch} onChange={(event) => setPermissionSearch(event.target.value)} placeholder="Search permissions" />
                                <div className="grid max-h-80 gap-2 overflow-auto pr-1 md:grid-cols-2 xl:grid-cols-3">
                                    {visiblePermissions.map((permission) => (
                                        <label key={permission} className="flex items-center gap-2 text-sm">
                                            <Checkbox checked={form.data.permissions.includes(permission)} onCheckedChange={(checked) => form.setData('permissions', toggleValue(form.data.permissions, permission, Boolean(checked)))} />
                                            <span>{permission}</span>
                                            {permission.includes('delete') ? <Badge variant="outline">critical</Badge> : null}
                                        </label>
                                    ))}
                                </div>
                                <InputError message={form.errors.permissions} />
                            </CardContent>
                        </Card>
                    ) : null}

                    {formErrors.users ? (
                        <Alert variant="destructive" className="lg:col-span-3">
                            <AlertTitle>Unable to update user</AlertTitle>
                            <AlertDescription>{formErrors.users}</AlertDescription>
                        </Alert>
                    ) : null}

                    <div className="flex gap-2 lg:col-span-3">
                        <Button type="submit" disabled={form.processing}>Save changes</Button>
                        <Button type="button" variant="outline" onClick={() => form.reset('password', 'password_confirmation')}>Clear password fields</Button>
                    </div>
                </form>
            </div>
        </>
    );
}
