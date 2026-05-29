import { Head } from '@inertiajs/react';

export default function JournalEntryShow({ entry }: { entry: Record<string, unknown> }) {
    return (
        <>
            <Head title="Journal Entry" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <h1 className="text-2xl font-semibold">Journal Entry #{String(entry.id)}</h1>
                <pre className="overflow-auto rounded-lg border p-4 text-xs">{JSON.stringify(entry, null, 2)}</pre>
            </div>
        </>
    );
}

JournalEntryShow.layout = {
    breadcrumbs: [{ title: 'Journal Entry', href: '/accounting/journal-entries' }],
};
