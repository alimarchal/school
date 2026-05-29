import { Head, Link, router, useForm } from '@inertiajs/react';
import { Edit, Eye, Filter, Plus } from 'lucide-react';
import type { FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

type Entry = {
    id: number;
    entry_date: string;
    reference: string | null;
    description: string | null;
    status: string;
};

type Props = {
    entries: { data: Entry[] };
    filters: Record<string, string | undefined>;
    currencies: Array<{ id: number; code: string; name: string }>;
};

export default function JournalEntriesIndex({ entries, filters, currencies }: Props) {
    const filterForm = useForm({
        reference: filters.reference ?? '',
        description: filters.description ?? '',
        status: filters.status ?? 'all',
        currency_id: filters.currency_id ?? 'all',
        entry_date_from: filters.entry_date_from ?? '',
        entry_date_to: filters.entry_date_to ?? '',
    });

    const submitFilters = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const params = Object.fromEntries(
            Object.entries(filterForm.data)
                .filter(([, value]) => value !== '' && value !== 'all')
                .map(([key, value]) => [`filter[${key}]`, value]),
        );

        router.get('/accounting/journal-entries', params, {
            preserveState: true,
            replace: true,
        });
    };

    return (
        <>
            <Head title="Journal Entries" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <h1 className="text-2xl font-semibold">Journal Entries</h1>
                    <Button asChild>
                        <Link href="/accounting/journal-entries/create">
                            <Plus className="size-4" />
                            New journal
                        </Link>
                    </Button>
                </div>
                <form onSubmit={submitFilters} className="grid gap-3 rounded-lg border p-3 md:grid-cols-6">
                    <div className="flex flex-col gap-2">
                        <Label htmlFor="reference">Reference</Label>
                        <Input id="reference" value={filterForm.data.reference} onChange={(event) => filterForm.setData('reference', event.target.value)} />
                    </div>
                    <div className="flex flex-col gap-2">
                        <Label htmlFor="description">Description</Label>
                        <Input id="description" value={filterForm.data.description} onChange={(event) => filterForm.setData('description', event.target.value)} />
                    </div>
                    <div className="flex flex-col gap-2">
                        <Label>Status</Label>
                        <Select value={filterForm.data.status} onValueChange={(value) => filterForm.setData('status', value)}>
                            <SelectTrigger className="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All</SelectItem>
                                <SelectItem value="draft">Draft</SelectItem>
                                <SelectItem value="posted">Posted</SelectItem>
                                <SelectItem value="void">Void</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="flex flex-col gap-2">
                        <Label>Currency</Label>
                        <Select value={filterForm.data.currency_id} onValueChange={(value) => filterForm.setData('currency_id', value)}>
                            <SelectTrigger className="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All</SelectItem>
                                {currencies.map((currency) => (
                                    <SelectItem key={currency.id} value={String(currency.id)}>
                                        {currency.code} - {currency.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="flex flex-col gap-2">
                        <Label htmlFor="entry_date_from">From</Label>
                        <Input id="entry_date_from" type="date" value={filterForm.data.entry_date_from} onChange={(event) => filterForm.setData('entry_date_from', event.target.value)} />
                    </div>
                    <div className="flex flex-col gap-2">
                        <Label htmlFor="entry_date_to">To</Label>
                        <Input id="entry_date_to" type="date" value={filterForm.data.entry_date_to} onChange={(event) => filterForm.setData('entry_date_to', event.target.value)} />
                    </div>
                    <div className="flex items-end md:col-span-6">
                        <Button type="submit" variant="outline">
                            <Filter className="size-4" />
                            Apply filters
                        </Button>
                    </div>
                </form>
                <div className="overflow-hidden rounded-lg border">
                    <table className="w-full min-w-[760px] text-sm">
                        <thead className="bg-muted/50 text-left">
                            <tr>
                                <th className="p-3">Date</th>
                                <th className="p-3">Reference</th>
                                <th className="p-3">Description</th>
                                <th className="p-3">Status</th>
                                <th className="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {entries.data.map((entry) => (
                                <tr key={entry.id} className="border-t">
                                    <td className="p-3">{entry.entry_date}</td>
                                    <td className="p-3">{entry.reference}</td>
                                    <td className="p-3">{entry.description}</td>
                                    <td className="p-3">{entry.status}</td>
                                    <td className="p-3">
                                        <div className="flex justify-end gap-2">
                                            {entry.status === 'draft' && (
                                                <Button asChild size="icon" variant="ghost" title="Edit">
                                                    <Link href={`/accounting/journal-entries/${entry.id}/edit`}>
                                                        <Edit className="size-4" />
                                                    </Link>
                                                </Button>
                                            )}
                                            <Button asChild size="icon" variant="ghost" title="View">
                                                <Link href={`/accounting/journal-entries/${entry.id}`}>
                                                    <Eye className="size-4" />
                                                </Link>
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

JournalEntriesIndex.layout = {
    breadcrumbs: [{ title: 'Journal Entries', href: '/accounting/journal-entries' }],
};
