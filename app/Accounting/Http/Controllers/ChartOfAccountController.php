<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Models\ChartOfAccount;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ChartOfAccountController extends Controller
{
    public function index(): Response
    {
        $accounts = QueryBuilder::for(ChartOfAccount::query()->with(['accountType', 'currency', 'parent']))
            ->allowedFilters([
                AllowedFilter::partial('account_code'),
                AllowedFilter::partial('account_name'),
                AllowedFilter::exact('account_type_id'),
                AllowedFilter::exact('currency_id'),
                AllowedFilter::exact('is_group'),
                AllowedFilter::exact('is_active'),
            ])
            ->orderBy('account_code')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('accounting/chart-of-accounts/index', [
            'accounts' => $accounts,
        ]);
    }

    public function tree(): Response
    {
        return Inertia::render('accounting/chart-of-accounts/tree', [
            'roots' => ChartOfAccount::query()
                ->with(['accountType', 'childrenRecursive'])
                ->whereNull('parent_id')
                ->orderBy('account_code')
                ->get(),
        ]);
    }
}
