import { Head, Link, router, useForm } from '@inertiajs/react';
import { Edit, Eye, Plus, Trash2 } from 'lucide-react';
import type { FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

type RecordValue = string | number | boolean | null;

type AccountingRecord = {
    id: number;
    [key: string]: RecordValue;
};

type Props = {
    title: string;
    routeName: string;
    columns: string[];
    fields: Array<{
        name: string;
        label: string;
        type: string;
        options?: Record<string, string>;
        filter?: boolean;
        table?: boolean;
    }>;
    filters: Record<string, string>;
    readOnly?: boolean;
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

export default function AccountingResourceIndex({ title, routeName, columns, fields, filters, readOnly = false, records }: Props) {
    const basePath = `/accounting/${routeName}`;
    const filterFields = fields.filter((field) => field.filter ?? field.table);
    const filterForm = useForm<Record<string, string>>(
        Object.fromEntries(filterFields.map((field) => [field.name, filters?.[field.name] ?? ''])) as Record<string, string>,
    );

    const destroy = (record: AccountingRecord) => {
        if (window.confirm(`Delete ${title.toLowerCase()} #${record.id}?`)) {
            router.delete(`${basePath}/${record.id}`);
        }
    };

    const submitFilters = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        router.get(
            basePath,
            Object.fromEntries(
                Object.entries(filterForm.data)
                    .filter(([, value]) => value !== '')
                    .map(([key, value]) => [`filter[${key}]`, value]),
            ),
            { preserveState: true, replace: true },
        );
    };

    return (
        <>
            <Head title={`${title}s`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <h1 className="text-2xl font-semibold">{title}s</h1>
                    {!readOnly && (
                        <Button asChild>
                            <Link href={`${basePath}/create`}>
                                <Plus className="size-4" />
                                New
                            </Link>
                        </Button>
                    )}
                </div>

                {filterFields.length > 0 && (
                    <form onSubmit={submitFilters} className="grid grid-cols-1 gap-3 rounded-lg border p-3 md:grid-cols-4">
                        {filterFields.map((field) => (
                            <div key={field.name} className="flex flex-col gap-2">
                                <Label>{field.label}</Label>
                                {field.type === 'select' || field.type === 'checkbox' ? (
                                    <Select value={filterForm.data[field.name] || 'all'} onValueChange={(value) => filterForm.setData(field.name, value === 'all' ? '' : value)}>
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="All" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">All</SelectItem>
                                            {field.type === 'checkbox' ? (
                                                <>
                                                    <SelectItem value="1">Yes</SelectItem>
                                                    <SelectItem value="0">No</SelectItem>
                                                </>
                                            ) : (
                                                Object.entries(field.options ?? {}).map(([value, label]) => (
                                                    <SelectItem key={value} value={String(value)}>
                                                        {label}
                                                    </SelectItem>
                                                ))
                                            )}
                                        </SelectContent>
                                    </Select>
                                ) : (
                                    <Input value={filterForm.data[field.name] ?? ''} onChange={(event) => filterForm.setData(field.name, event.target.value)} />
                                )}
                            </div>
                        ))}
                        <div className="flex items-end gap-2">
                            <Button type="submit">Filter</Button>
                            <Button asChild variant="outline">
                                <Link href={basePath}>Reset</Link>
                            </Button>
                        </div>
                    </form>
                )}

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
                                            {!readOnly && (
                                                <>
                                                    <Button asChild size="icon" variant="ghost" title="Edit">
                                                        <Link href={`${basePath}/${record.id}/edit`}>
                                                            <Edit className="size-4" />
                                                        </Link>
                                                    </Button>
                                                    <Button size="icon" variant="ghost" title="Delete" onClick={() => destroy(record)}>
                                                        <Trash2 className="size-4" />
                                                    </Button>
                                                </>
                                            )}
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
