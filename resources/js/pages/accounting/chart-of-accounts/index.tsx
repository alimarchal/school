import { Head, Link, router, useForm } from '@inertiajs/react';
import { Edit, Filter, Plus, Trash2 } from 'lucide-react';
import type { FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

type Account = {
    id: number;
    account_code: string;
    account_name: string;
    normal_balance: string;
    is_group: boolean;
    is_active: boolean;
};

type Props = {
    accounts: {
        data: Account[];
    };
    filters: Record<string, string | undefined>;
    accountTypes: Array<{ id: number; name: string }>;
    currencies: Array<{ id: number; code: string; name: string }>;
};

export default function ChartOfAccountsIndex({ accounts, filters, accountTypes, currencies }: Props) {
    const filterForm = useForm({
        account_code: filters.account_code ?? '',
        account_name: filters.account_name ?? '',
        account_type_id: filters.account_type_id ?? 'all',
        currency_id: filters.currency_id ?? 'all',
        is_group: filters.is_group ?? 'all',
        is_active: filters.is_active ?? 'all',
    });

    const submitFilters = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const params = Object.fromEntries(
            Object.entries(filterForm.data)
                .filter(([, value]) => value !== '' && value !== 'all')
                .map(([key, value]) => [`filter[${key}]`, value]),
        );

        router.get('/accounting/chart-of-accounts', params, {
            preserveState: true,
            replace: true,
        });
    };

    const destroy = (account: Account) => {
        if (window.confirm(`Delete account ${account.account_code}?`)) {
            router.delete(`/accounting/chart-of-accounts/${account.id}`);
        }
    };

    return (
        <>
            <Head title="Chart of Accounts" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <h1 className="text-2xl font-semibold">Chart of Accounts</h1>
                    <Button asChild>
                        <Link href="/accounting/chart-of-accounts/create">
                            <Plus className="size-4" />
                            New account
                        </Link>
                    </Button>
                </div>
                <form onSubmit={submitFilters} className="grid gap-3 rounded-lg border p-3 md:grid-cols-6">
                    <div className="flex flex-col gap-2">
                        <Label htmlFor="account_code">Code</Label>
                        <Input id="account_code" value={filterForm.data.account_code} onChange={(event) => filterForm.setData('account_code', event.target.value)} />
                    </div>
                    <div className="flex flex-col gap-2">
                        <Label htmlFor="account_name">Name</Label>
                        <Input id="account_name" value={filterForm.data.account_name} onChange={(event) => filterForm.setData('account_name', event.target.value)} />
                    </div>
                    <div className="flex flex-col gap-2">
                        <Label>Type</Label>
                        <Select value={filterForm.data.account_type_id} onValueChange={(value) => filterForm.setData('account_type_id', value)}>
                            <SelectTrigger className="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All</SelectItem>
                                {accountTypes.map((type) => (
                                    <SelectItem key={type.id} value={String(type.id)}>
                                        {type.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="flex flex-col gap-2">
                        <Label>Currency</Label>
                        <Select value={filterForm.data.currency_id} onValueChange={(value) => filterForm.setData('currency_id', value)}>
                            <SelectTrigger className="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All</SelectItem>
                                {currencies.map((currency) => (
                                    <SelectItem key={currency.id} value={String(currency.id)}>
                                        {currency.code} - {currency.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="flex flex-col gap-2">
                        <Label>Posting</Label>
                        <Select value={filterForm.data.is_group} onValueChange={(value) => filterForm.setData('is_group', value)}>
                            <SelectTrigger className="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All</SelectItem>
                                <SelectItem value="0">Posting only</SelectItem>
                                <SelectItem value="1">Group only</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="flex flex-col gap-2">
                        <Label>Status</Label>
                        <Select value={filterForm.data.is_active} onValueChange={(value) => filterForm.setData('is_active', value)}>
                            <SelectTrigger className="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All</SelectItem>
                                <SelectItem value="1">Active</SelectItem>
                                <SelectItem value="0">Inactive</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="flex items-end md:col-span-6">
                        <Button type="submit" variant="outline">
                            <Filter className="size-4" />
                            Apply filters
                        </Button>
                    </div>
                </form>
                <div className="overflow-hidden rounded-lg border">
                    <table className="w-full min-w-[760px] text-sm">
                        <thead className="bg-muted/50 text-left">
                            <tr>
                                <th className="p-3">Code</th>
                                <th className="p-3">Name</th>
                                <th className="p-3">Normal</th>
                                <th className="p-3">Type</th>
                                <th className="p-3">Status</th>
                                <th className="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {accounts.data.map((account) => (
                                <tr key={account.id} className="border-t">
                                    <td className="p-3 font-mono">{account.account_code}</td>
                                    <td className="p-3">{account.account_name}</td>
                                    <td className="p-3">{account.normal_balance}</td>
                                    <td className="p-3">{account.is_group ? 'Group' : 'Posting'}</td>
                                    <td className="p-3">{account.is_active ? 'Active' : 'Inactive'}</td>
                                    <td className="p-3">
                                        <div className="flex justify-end gap-2">
                                            <Button asChild size="icon" variant="ghost" title="Edit">
                                                <Link href={`/accounting/chart-of-accounts/${account.id}/edit`}>
                                                    <Edit className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button size="icon" variant="ghost" title="Delete" onClick={() => destroy(account)}>
                                                <Trash2 className="size-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}

ChartOfAccountsIndex.layout = {
    breadcrumbs: [{ title: 'Chart of Accounts', href: '/accounting/chart-of-accounts' }],
};
