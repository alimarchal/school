import { Head, Link, useForm } from '@inertiajs/react';
import { Plus, Save, Trash2 } from 'lucide-react';
import type { FormEvent } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

type Account = {
    id: number;
    account_code: string;
    account_name: string;
};

type Currency = {
    id: number;
    code: string;
    name: string;
    is_base: boolean;
};

type CostCenter = {
    id: number;
    code: string;
    name: string;
};

type JournalLine = {
    chart_of_account_id: string;
    cost_center_id: string;
    debit: string;
    credit: string;
    description: string;
};

type Props = {
    action: string;
    accounts: Account[];
    currencies: Currency[];
    costCenters: CostCenter[];
};

const emptyLine = (): JournalLine => ({
    chart_of_account_id: '',
    cost_center_id: 'none',
    debit: '0',
    credit: '0',
    description: '',
});

export default function JournalEntryForm({ action, accounts, currencies, costCenters }: Props) {
    const baseCurrency = currencies.find((currency) => currency.is_base) ?? currencies[0];
    const today = new Date().toISOString().slice(0, 10);

    const form = useForm({
        entry_date: today,
        currency_id: baseCurrency ? String(baseCurrency.id) : '',
        fx_rate_to_base: '1',
        reference: '',
        description: '',
        auto_post: false,
        lines: [emptyLine(), emptyLine()],
    });

    const setLine = (index: number, field: keyof JournalLine, value: string) => {
        form.setData(
            'lines',
            form.data.lines.map((line, lineIndex) => (lineIndex === index ? { ...line, [field]: value } : line)),
        );
    };

    const addLine = () => {
        form.setData('lines', [...form.data.lines, emptyLine()]);
    };

    const removeLine = (index: number) => {
        if (form.data.lines.length <= 2) {
            return;
        }

        form.setData(
            'lines',
            form.data.lines.filter((_, lineIndex) => lineIndex !== index),
        );
    };

    const submit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        form.transform((data) => ({
            ...data,
            lines: data.lines.map((line) => ({
                ...line,
                cost_center_id: line.cost_center_id === 'none' ? '' : line.cost_center_id,
                debit: line.debit === '' ? '0' : line.debit,
                credit: line.credit === '' ? '0' : line.credit,
            })),
        }));

        form.post(action);
    };

    return (
        <>
            <Head title="Create Journal Entry" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <h1 className="text-2xl font-semibold">Create Journal Entry</h1>
                    <Button asChild variant="outline">
                        <Link href="/accounting/journal-entries">Back</Link>
                    </Button>
                </div>

                <form onSubmit={submit} className="flex max-w-6xl flex-col gap-4 rounded-lg border p-4">
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div className="flex flex-col gap-2">
                            <Label htmlFor="entry_date">Entry Date</Label>
                            <Input id="entry_date" type="date" value={form.data.entry_date} onChange={(event) => form.setData('entry_date', event.target.value)} />
                            <InputError message={form.errors.entry_date} />
                        </div>

                        <div className="flex flex-col gap-2">
                            <Label>Currency</Label>
                            <Select value={form.data.currency_id} onValueChange={(value) => form.setData('currency_id', value)}>
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Select currency" />
                                </SelectTrigger>
                                <SelectContent>
                                    {currencies.map((currency) => (
                                        <SelectItem key={currency.id} value={String(currency.id)}>
                                            {currency.code} - {currency.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={form.errors.currency_id} />
                        </div>

                        <div className="flex flex-col gap-2">
                            <Label htmlFor="fx_rate_to_base">FX Rate</Label>
                            <Input id="fx_rate_to_base" type="number" step="0.00000001" value={form.data.fx_rate_to_base} onChange={(event) => form.setData('fx_rate_to_base', event.target.value)} />
                            <InputError message={form.errors.fx_rate_to_base} />
                        </div>

                        <div className="flex flex-col gap-2">
                            <Label htmlFor="reference">Reference</Label>
                            <Input id="reference" value={form.data.reference} onChange={(event) => form.setData('reference', event.target.value)} />
                            <InputError message={form.errors.reference} />
                        </div>
                    </div>

                    <div className="flex flex-col gap-2">
                        <Label htmlFor="description">Description</Label>
                        <textarea
                            id="description"
                            value={form.data.description}
                            onChange={(event) => form.setData('description', event.target.value)}
                            className="border-input bg-background min-h-24 rounded-md border px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        />
                        <InputError message={form.errors.description} />
                    </div>

                    <div className="overflow-hidden rounded-lg border">
                        <table className="w-full min-w-[980px] text-sm">
                            <thead className="bg-muted/50 text-left">
                                <tr>
                                    <th className="p-3">Account</th>
                                    <th className="p-3">Cost Center</th>
                                    <th className="p-3">Debit</th>
                                    <th className="p-3">Credit</th>
                                    <th className="p-3">Description</th>
                                    <th className="w-12 p-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                {form.data.lines.map((line, index) => (
                                    <tr key={index} className="border-t">
                                        <td className="p-3">
                                            <Select value={line.chart_of_account_id} onValueChange={(value) => setLine(index, 'chart_of_account_id', value)}>
                                                <SelectTrigger className="w-full">
                                                    <SelectValue placeholder="Select account" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {accounts.map((account) => (
                                                        <SelectItem key={account.id} value={String(account.id)}>
                                                            {account.account_code} - {account.account_name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            <InputError message={form.errors[`lines.${index}.chart_of_account_id`]} />
                                        </td>
                                        <td className="p-3">
                                            <Select value={line.cost_center_id} onValueChange={(value) => setLine(index, 'cost_center_id', value)}>
                                                <SelectTrigger className="w-full">
                                                    <SelectValue placeholder="Cost center" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="none">None</SelectItem>
                                                    {costCenters.map((costCenter) => (
                                                        <SelectItem key={costCenter.id} value={String(costCenter.id)}>
                                                            {costCenter.code} - {costCenter.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            <InputError message={form.errors[`lines.${index}.cost_center_id`]} />
                                        </td>
                                        <td className="p-3">
                                            <Input type="number" step="0.01" value={line.debit} onChange={(event) => setLine(index, 'debit', event.target.value)} />
                                            <InputError message={form.errors[`lines.${index}.debit`]} />
                                        </td>
                                        <td className="p-3">
                                            <Input type="number" step="0.01" value={line.credit} onChange={(event) => setLine(index, 'credit', event.target.value)} />
                                            <InputError message={form.errors[`lines.${index}.credit`]} />
                                        </td>
                                        <td className="p-3">
                                            <Input value={line.description} onChange={(event) => setLine(index, 'description', event.target.value)} />
                                            <InputError message={form.errors[`lines.${index}.description`]} />
                                        </td>
                                        <td className="p-3">
                                            <Button type="button" size="icon" variant="ghost" onClick={() => removeLine(index)} disabled={form.data.lines.length <= 2}>
                                                <Trash2 className="size-4" />
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <Button type="button" variant="outline" onClick={addLine}>
                            <Plus className="size-4" />
                            Add line
                        </Button>
                        <div className="flex items-center gap-3">
                            <Checkbox id="auto_post" checked={form.data.auto_post} onCheckedChange={(checked) => form.setData('auto_post', checked === true)} />
                            <Label htmlFor="auto_post">Post after saving</Label>
                        </div>
                    </div>

                    <div className="flex justify-end">
                        <Button type="submit" disabled={form.processing}>
                            <Save className="size-4" />
                            Save journal
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

JournalEntryForm.layout = {
    breadcrumbs: [{ title: 'Journal Entries', href: '/accounting/journal-entries' }],
};
