import { Head, Link, router } from '@inertiajs/react';
import { Edit, Eye, Plus, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';

type RecordValue = string | number | boolean | null;

type AccountingRecord = {
    id: number;
    [key: string]: RecordValue;
};

type Props = {
    title: string;
    routeName: string;
    columns: string[];
    records: {
        data: AccountingRecord[];
    };
};

function displayValue(value: RecordValue): string {
    if (typeof value === 'boolean') {
        return value ? 'Yes' : 'No';
    }

    return value === null ? '' : String(value);
}

export default function AccountingResourceIndex({ title, routeName, columns, records }: Props) {
    const basePath = `/accounting/${routeName}`;

    const destroy = (record: AccountingRecord) => {
        if (window.confirm(`Delete ${title.toLowerCase()} #${record.id}?`)) {
            router.delete(`${basePath}/${record.id}`);
        }
    };

    return (
        <>
            <Head title={`${title}s`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <h1 className="text-2xl font-semibold">{title}s</h1>
                    <Button asChild>
                        <Link href={`${basePath}/create`}>
                            <Plus className="size-4" />
                            New
                        </Link>
                    </Button>
                </div>

                <div className="overflow-hidden rounded-lg border">
                    <table className="w-full min-w-[760px] text-sm">
                        <thead className="bg-muted/50 text-left">
                            <tr>
                                {columns.map((column) => (
                                    <th key={column} className="p-3 font-medium capitalize">
                                        {column.replaceAll('_', ' ')}
                                    </th>
                                ))}
                                <th className="w-40 p-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {records.data.map((record) => (
                                <tr key={record.id} className="border-t">
                                    {columns.map((column) => (
                                        <td key={column} className="p-3">
                                            {displayValue(record[column])}
                                        </td>
                                    ))}
                                    <td className="p-3">
                                        <div className="flex justify-end gap-2">
                                            <Button asChild size="icon" variant="ghost" title="View">
                                                <Link href={`${basePath}/${record.id}`}>
                                                    <Eye className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button asChild size="icon" variant="ghost" title="Edit">
                                                <Link href={`${basePath}/${record.id}/edit`}>
                                                    <Edit className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button size="icon" variant="ghost" title="Delete" onClick={() => destroy(record)}>
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

AccountingResourceIndex.layout = {
    breadcrumbs: [{ title: 'Accounting', href: '/accounting' }],
};
