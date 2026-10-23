<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class GlobalSearchController extends Controller
{
    /**
     * Search the main POS records without assuming every optional column exists.
     * Candidate columns are checked against the actual database schema first.
     */
    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $results = collect();

        if (mb_strlen($query) >= 2) {
            $searchTargets = [
                'Products' => [
                    'table' => 'products',
                    'search' => ['name', 'product_name', 'title', 'sku', 'barcode', 'code'],
                    'title' => ['name', 'product_name', 'title'],
                    'detail' => ['sku', 'barcode', 'code', 'price', 'stock', 'quantity'],
                    'routes' => ['products.show', 'product.show', 'products.edit', 'product.edit'],
                ],
                'Sales / Orders' => [
                    'table' => 'orders',
                    'search' => ['id', 'order_number', 'invoice_number', 'code', 'status'],
                    'title' => ['order_number', 'invoice_number', 'code', 'id'],
                    'detail' => ['status', 'total', 'created_at'],
                    'routes' => ['orders.show', 'order.show', 'orders.edit', 'order.edit'],
                ],
                'Customers' => [
                    'table' => 'users',
                    'search' => ['name', 'full_name', 'email', 'phone', 'mobile'],
                    'title' => ['name', 'full_name', 'email'],
                    'detail' => ['email', 'phone', 'mobile'],
                    'routes' => ['customers.show', 'customer.show', 'users.show', 'user.show', 'customers.edit', 'customer.edit'],
                ],
                'Suppliers' => [
                    'table' => 'suppliers',
                    'search' => ['name', 'company_name', 'email', 'phone', 'mobile', 'code'],
                    'title' => ['name', 'company_name', 'code'],
                    'detail' => ['email', 'phone', 'mobile'],
                    'routes' => ['suppliers.show', 'supplier.show', 'suppliers.edit', 'supplier.edit'],
                ],
                'Purchases' => [
                    'table' => 'purchases',
                    'search' => ['id', 'invoice_number', 'reference', 'status'],
                    'title' => ['invoice_number', 'reference', 'id'],
                    'detail' => ['status', 'total_price', 'created_at'],
                    'routes' => ['purchases.show', 'purchase.show', 'purchases.edit', 'purchase.edit'],
                ],
                'Installments' => [
                    'table' => 'installments',
                    'search' => ['id', 'status', 'installment_number'],
                    'title' => ['installment_number', 'id'],
                    'detail' => ['status', 'amount', 'remaining_amount', 'start_date'],
                    'routes' => ['installments.show', 'installment.show'],
                ],
            ];

            foreach ($searchTargets as $label => $target) {
                foreach ($this->searchTable($target, $query) as $record) {
                    $results->push([
                        'group' => $label,
                        'title' => $record['title'],
                        'detail' => $record['detail'],
                        'url' => $record['url'],
                    ]);
                }
            }
        }

        return view('search.index', [
            'query' => $query,
            'results' => $results,
            'hasSearched' => $query !== '',
        ]);
    }

    private function searchTable(array $target, string $searchTerm): array
    {
        $table = $target['table'];

        if (!Schema::hasTable($table)) {
            return [];
        }

        $columns = Schema::getColumnListing($table);
        $availableSearchColumns = array_values(array_intersect($target['search'], $columns));

        if ($availableSearchColumns === []) {
            return [];
        }

        $records = DB::table($table)
            ->where(function (Builder $builder) use ($availableSearchColumns, $searchTerm, $columns): void {
                foreach ($availableSearchColumns as $column) {
                    if ($column === 'id' && ctype_digit($searchTerm)) {
                        $builder->orWhere($column, (int) $searchTerm);
                    } else {
                        $builder->orWhere($column, 'like', '%' . addcslashes($searchTerm, '%_\\') . '%');
                    }
                }
            })
            ->orderByDesc(in_array('created_at', $columns, true) ? 'created_at' : 'id')
            ->limit(6)
            ->get();

        return $records->map(function (object $record) use ($target): array {
            $id = $record->id ?? null;
            $title = $this->firstValue($record, $target['title']) ?? ($target['table'] . ' #' . $id);
            $detail = $this->firstValue($record, $target['detail']);

            return [
                'title' => (string) $title,
                'detail' => $detail === null ? null : (string) $detail,
                'url' => $id === null ? null : $this->firstAvailableRoute($target['routes'], $id),
            ];
        })->all();
    }

    private function firstValue(object $record, array $candidates): mixed
    {
        foreach ($candidates as $column) {
            if (isset($record->{$column}) && $record->{$column} !== '') {
                return $record->{$column};
            }
        }

        return null;
    }

    private function firstAvailableRoute(array $routeNames, int|string $id): ?string
    {
        foreach ($routeNames as $routeName) {
            if (Route::has($routeName)) {
                try {
                    return route($routeName, $id);
                } catch (\Throwable) {
                    // Skip routes whose required parameters are incompatible.
                }
            }
        }

        return null;
    }
}
