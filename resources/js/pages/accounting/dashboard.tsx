import { Head } from '@inertiajs/react';

type Props = {
    summary: Record<string, number>;
};

export default function AccountingDashboard({ summary }: Props) {
    return (
        <>
            <Head title="Accounting" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div>
                    <h1 className="text-2xl font-semibold">Accounting</h1>
                    <p className="text-sm text-muted-foreground">General ledger, chart of accounts, periods, and financial reports.</p>
                </div>
                <div className="grid gap-3 md:grid-cols-4">
                    {Object.entries(summary).map(([label, value]) => (
                        <div key={label} className="rounded-lg border p-4">
                            <div className="text-sm text-muted-foreground">{label}</div>
                            <div className="mt-2 text-2xl font-semibold">{value}</div>
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}

AccountingDashboard.layout = {
    breadcrumbs: [{ title: 'Accounting', href: '/accounting' }],
};
