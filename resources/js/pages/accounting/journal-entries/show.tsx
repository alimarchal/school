import { Head, Link, router, usePage } from '@inertiajs/react';
import { Ban, Edit, RotateCcw, Send } from 'lucide-react';
import { Button } from '@/components/ui/button';

type JournalLine = {
    id: number;
    line_no: number;
    debit: string;
    credit: string;
    description: string | null;
    account?: {
        account_code: string;
        account_name: string;
    };
    cost_center?: {
        code: string;
        name: string;
    } | null;
};

type JournalEntry = {
    id: number;
    entry_date: string;
    reference: string | null;
    description: string | null;
    status: 'draft' | 'posted' | 'void';
    posted_at: string | null;
    lines: JournalLine[];
    currency?: {
        code: string;
    };
};

export default function JournalEntryShow({ entry }: { entry: JournalEntry }) {
    const { auth } = usePage().props;
    const permissions = auth.accountingPermissions ?? {};
    const canEdit = permissions['journal-entries.update'] === true && entry.status === 'draft';
    const canPost = permissions['journal-entries.post'] === true && entry.status === 'draft';
    const canReverse = permissions['journal-entries.reverse'] === true && entry.status === 'posted';
    const canVoid = permissions['journal-entries.void'] === true && entry.status === 'draft';

    const post = () => router.post(`/accounting/journal-entries/${entry.id}/post`);
    const reverse = () => router.post(`/accounting/journal-entries/${entry.id}/reverse`);
    const voidEntry = () => router.post(`/accounting/journal-entries/${entry.id}/void`);

    return (
        <>
            <Head title={`Journal Entry #${entry.id}`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">Journal Entry #{entry.id}</h1>
                        <p className="text-sm text-muted-foreground">
                            {entry.entry_date} · {entry.reference ?? 'No reference'} · {entry.currency?.code ?? 'Base currency'}
                        </p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <Button asChild variant="outline">
                            <Link href="/accounting/journal-entries">Back</Link>
                        </Button>
                        {canEdit && (
                            <Button asChild variant="outline">
                                <Link href={`/accounting/journal-entries/${entry.id}/edit`}>
                                    <Edit className="size-4" />
                                    Edit
                                </Link>
                            </Button>
                        )}
                        {canPost && (
                            <Button onClick={post}>
                                <Send className="size-4" />
                                Post
                            </Button>
                        )}
                        {canReverse && (
                            <Button onClick={reverse} variant="outline">
                                <RotateCcw className="size-4" />
                                Reverse
                            </Button>
                        )}
                        {canVoid && (
                            <Button onClick={voidEntry} variant="destructive">
                                <Ban className="size-4" />
                                Void
                            </Button>
                        )}
                    </div>
                </div>

                <div className="grid gap-3 md:grid-cols-4">
                    <div className="rounded-lg border p-3">
                        <div className="text-sm text-muted-foreground">Status</div>
                        <div className="mt-1 font-semibold capitalize">{entry.status}</div>
                    </div>
                    <div className="rounded-lg border p-3">
                        <div className="text-sm text-muted-foreground">Posted At</div>
                        <div className="mt-1 font-semibold">{entry.posted_at ?? '-'}</div>
                    </div>
                    <div className="rounded-lg border p-3 md:col-span-2">
                        <div className="text-sm text-muted-foreground">Description</div>
                        <div className="mt-1 font-semibold">{entry.description ?? '-'}</div>
                    </div>
                </div>

                <div className="overflow-hidden rounded-lg border">
                    <table className="w-full min-w-[860px] text-sm">
                        <thead className="bg-muted/50 text-left">
                            <tr>
                                <th className="p-3">#</th>
                                <th className="p-3">Account</th>
                                <th className="p-3">Cost Center</th>
                                <th className="p-3 text-right">Debit</th>
                                <th className="p-3 text-right">Credit</th>
                                <th className="p-3">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            {entry.lines.map((line) => (
                                <tr key={line.id} className="border-t">
                                    <td className="p-3">{line.line_no}</td>
                                    <td className="p-3">
                                        {line.account?.account_code} - {line.account?.account_name}
                                    </td>
                                    <td className="p-3">{line.cost_center ? `${line.cost_center.code} - ${line.cost_center.name}` : '-'}</td>
                                    <td className="p-3 text-right font-mono">{line.debit}</td>
                                    <td className="p-3 text-right font-mono">{line.credit}</td>
                                    <td className="p-3">{line.description ?? '-'}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}

JournalEntryShow.layout = {
    breadcrumbs: [{ title: 'Journal Entry', href: '/accounting/journal-entries' }],
};
