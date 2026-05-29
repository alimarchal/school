<?php

namespace App\Accounting\Http\Controllers\Api;

use App\Accounting\Http\Resources\AccountResource;
use App\Accounting\Models\ChartOfAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChartOfAccountApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return AccountResource::collection(
            ChartOfAccount::query()->with(['accountType', 'currency'])->orderBy('account_code')->paginate()
        );
    }
}
