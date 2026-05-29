<?php

namespace App\Accounting\Http\Controllers\Reports;

use App\Accounting\Reports\AccountStatementReport;
use App\Accounting\Reports\AgedPayablesReport;
use App\Accounting\Reports\AgedReceivablesReport;
use App\Accounting\Reports\BalanceSheetReport;
use App\Accounting\Reports\CashFlowReport;
use App\Accounting\Reports\GeneralLedgerReport;
use App\Accounting\Reports\IncomeStatementReport;
use App\Accounting\Reports\TrialBalanceReport;
use App\Accounting\Services\AccountingReportExporter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportExportController extends Controller
{
    public function __invoke(Request $request, string $report, string $format, AccountingReportExporter $exporter): Response
    {
        abort_unless(in_array($format, ['csv', 'xlsx', 'pdf'], true), 404);

        [$permission, $rows] = match ($report) {
            'general-ledger' => ['reports.general-ledger.view', app(GeneralLedgerReport::class)->query($request->only(['date_from', 'date_to', 'account_id', 'status']))->get()],
            'trial-balance' => ['reports.trial-balance.view', app(TrialBalanceReport::class)->rows()],
            'balance-sheet' => ['reports.balance-sheet.view', app(BalanceSheetReport::class)->rows()],
            'income-statement' => ['reports.income-statement.view', app(IncomeStatementReport::class)->rows()],
            'cash-flow' => ['reports.cash-flow.view', app(CashFlowReport::class)->rows($request->only(['date_from', 'date_to']))],
            'aged-receivables' => ['reports.aged-receivables.view', app(AgedReceivablesReport::class)->rows()],
            'aged-payables' => ['reports.aged-payables.view', app(AgedPayablesReport::class)->rows()],
            'account-statement' => ['reports.account-statement.view', app(AccountStatementReport::class)->rows($request->only(['account_id', 'account_code', 'date_from', 'date_to']))],
            default => abort(404),
        };

        abort_unless($request->user()?->can($permission), 403);

        return $exporter->download($rows, $report, $format);
    }
}
