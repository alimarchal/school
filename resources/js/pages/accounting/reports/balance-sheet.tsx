import { Head } from '@inertiajs/react';

export default function BalanceSheet({ rows }: { rows: Array<Record<string, unknown>> }) {
    return (
        <>
            <Head title="Balance Sheet" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <h1 className="text-2xl font-semibold">Balance Sheet</h1>
                <pre className="overflow-auto rounded-lg border p-4 text-xs">{JSON.stringify(rows, null, 2)}</pre>
            </div>
        </>
    );
}

BalanceSheet.layout = {
    breadcrumbs: [{ title: 'Balance Sheet', href: '/accounting/reports/balance-sheet' }],
};
