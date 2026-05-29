import { Head } from '@inertiajs/react';

export default function TrialBalance({ rows, totals }: { rows: Array<Record<string, unknown>>; totals: Record<string, number> }) {
    return (
        <>
            <Head title="Trial Balance" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <h1 className="text-2xl font-semibold">Trial Balance</h1>
                <pre className="overflow-auto rounded-lg border p-4 text-xs">{JSON.stringify({ totals, rows }, null, 2)}</pre>
            </div>
        </>
    );
}

TrialBalance.layout = {
    breadcrumbs: [{ title: 'Trial Balance', href: '/accounting/reports/trial-balance' }],
};
