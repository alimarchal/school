import { Head, Link, router } from '@inertiajs/react';
import { Edit, Plus, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';

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
};

export default function ChartOfAccountsIndex({ accounts }: Props) {
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
