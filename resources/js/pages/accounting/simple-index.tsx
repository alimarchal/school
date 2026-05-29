import { Head } from '@inertiajs/react';

type Props = {
    records: {
        data: Array<Record<string, unknown>>;
    };
};

export default function SimpleAccountingIndex({ records }: Props) {
    const first = records.data[0] ?? {};
    const columns = Object.keys(first).filter((key) => !['created_at', 'updated_at', 'deleted_at'].includes(key));

    return (
        <>
            <Head title="Accounting Records" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <h1 className="text-2xl font-semibold">Accounting Records</h1>
                <div className="overflow-hidden rounded-lg border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/50 text-left">
                            <tr>{columns.map((column) => <th key={column} className="p-3 font-medium">{column}</th>)}</tr>
                        </thead>
                        <tbody>
                            {records.data.map((record, index) => (
                                <tr key={String(record.id ?? index)} className="border-t">
                                    {columns.map((column) => <td key={column} className="p-3">{String(record[column] ?? '')}</td>)}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}

SimpleAccountingIndex.layout = {
    breadcrumbs: [{ title: 'Accounting', href: '/accounting' }],
};
