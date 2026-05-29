import { Head, Link } from '@inertiajs/react';
import { Download } from 'lucide-react';
import { Button } from '@/components/ui/button';

export default function BalanceSheet({ rows }: { rows: Array<Record<string, unknown>> }) {
    return (
        <>
            <Head title="Balance Sheet" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <h1 className="text-2xl font-semibold">Balance Sheet</h1>
                    <div className="flex flex-wrap gap-2">
                        {['csv', 'xlsx', 'pdf'].map((format) => (
                            <Button key={format} asChild variant="outline">
                                <Link href={`/accounting/reports/balance-sheet/export/${format}`}>
                                    <Download className="size-4" />
                                    {format.toUpperCase()}
                                </Link>
                            </Button>
                        ))}
                    </div>
                </div>
                <pre className="overflow-auto rounded-lg border p-4 text-xs">{JSON.stringify(rows, null, 2)}</pre>
            </div>
        </>
    );
}

BalanceSheet.layout = {
    breadcrumbs: [{ title: 'Balance Sheet', href: '/accounting/reports/balance-sheet' }],
};
