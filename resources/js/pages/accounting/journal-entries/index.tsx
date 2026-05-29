import { Head, Link } from '@inertiajs/react';
import { Eye, Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';

type Entry = {
    id: number;
    entry_date: string;
    reference: string | null;
    description: string | null;
    status: string;
};

export default function JournalEntriesIndex({ entries }: { entries: { data: Entry[] } }) {
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
                                        <div className="flex justify-end">
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
