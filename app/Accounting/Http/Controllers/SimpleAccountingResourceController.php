<?php

namespace App\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\QueryBuilder;

abstract class SimpleAccountingResourceController extends Controller
{
    abstract protected function model(): string;

    abstract protected function page(): string;

    public function index(): Response
    {
        /** @var class-string<Model> $model */
        $model = $this->model();

        return Inertia::render($this->page(), [
            'records' => QueryBuilder::for($model::query())
                ->paginate(25)
                ->withQueryString(),
        ]);
    }
}
