import { Head, Link, usePage } from '@inertiajs/react';
import { Calculator, ShieldCheck } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

export default function Dashboard() {
    const { auth } = usePage().props;
    const canViewAccounting = auth.accountingPermissions?.['accounting.view'] === true;

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {canViewAccounting && (
                        <Card className="rounded-lg">
                            <CardHeader>
                                <div className="flex items-center gap-3">
                                    <div className="flex size-10 items-center justify-center rounded-md border">
                                        <Calculator className="size-5" />
                                    </div>
                                    <div>
                                        <CardTitle>Accounting</CardTitle>
                                        <CardDescription>Chart of accounts, journals, periods, reports, and audit logs.</CardDescription>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent className="flex flex-wrap gap-2">
                                <Button asChild>
                                    <Link href="/accounting">Open accounting</Link>
                                </Button>
                                <Button asChild variant="outline">
                                    <Link href="/accounting/journal-entries">Journal entries</Link>
                                </Button>
                            </CardContent>
                        </Card>
                    )}

                    <Card className="rounded-lg">
                        <CardHeader>
                            <div className="flex items-center gap-3">
                                <div className="flex size-10 items-center justify-center rounded-md border">
                                    <ShieldCheck className="size-5" />
                                </div>
                                <div>
                                    <CardTitle>Access Control</CardTitle>
                                    <CardDescription>Accounting routes are blocked unless the user has the matching permission.</CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
