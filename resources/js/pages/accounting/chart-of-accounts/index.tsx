import { Head } from '@inertiajs/react';

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
    return (
        <>
            <Head title="Chart of Accounts" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <h1 className="text-2xl font-semibold">Chart of Accounts</h1>
                <div className="overflow-hidden rounded-lg border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/50 text-left">
                            <tr>
                                <th className="p-3">Code</th>
                                <th className="p-3">Name</th>
                                <th className="p-3">Normal</th>
                                <th className="p-3">Type</th>
                                <th className="p-3">Status</th>
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
