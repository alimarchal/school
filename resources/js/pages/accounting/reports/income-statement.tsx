import { Head } from '@inertiajs/react';

export default function IncomeStatement({ rows }: { rows: Array<Record<string, unknown>> }) {
    return (
        <>
            <Head title="Income Statement" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <h1 className="text-2xl font-semibold">Income Statement</h1>
                <pre className="overflow-auto rounded-lg border p-4 text-xs">{JSON.stringify(rows, null, 2)}</pre>
            </div>
        </>
    );
}

IncomeStatement.layout = {
    breadcrumbs: [{ title: 'Income Statement', href: '/accounting/reports/income-statement' }],
};
