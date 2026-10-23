@extends('layouts.app')

@section('title', 'Global Search')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div>
                    <h1 class="m-0">Global Search</h1>
                    <p class="text-muted mb-0 mt-1">Search products, orders, customers, suppliers, purchases and installments.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <form method="GET" action="{{ route('global-search.index') }}" class="row">
                        <div class="col-md-10 mb-2 mb-md-0">
                            <label for="global-search-query" class="sr-only">Search term</label>
                            <input id="global-search-query" type="search" name="q" value="{{ $query }}" minlength="2" required class="form-control" placeholder="Enter a product name, SKU, order ID, customer email...">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Search</button>
                        </div>
                    </form>
                </div>
            </div>

            @if ($hasSearched && mb_strlen($query) < 2)
                <div class="alert alert-info">Enter at least two characters to search.</div>
            @elseif ($hasSearched && $results->isEmpty())
                <div class="card"><div class="card-body text-center py-5">
                    <i class="fas fa-search fa-2x text-muted mb-3"></i>
                    <h5>No results found</h5>
                    <p class="text-muted mb-0">No matching records were found for “{{ $query }}”.</p>
                </div></div>
            @elseif ($results->isNotEmpty())
                <div class="mb-3 text-muted">Showing up to six matches per category for <strong>{{ $query }}</strong>.</div>
                @foreach ($results->groupBy('group') as $group => $items)
                    <div class="card">
                        <div class="card-header"><h3 class="card-title font-weight-bold">{{ $group }}</h3><span class="badge badge-light float-right">{{ $items->count() }} result(s)</span></div>
                        <div class="list-group list-group-flush">
                            @foreach ($items as $item)
                                @if ($item['url'])
                                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ $item['url'] }}">
                                @else
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                @endif
                                    <div>
                                        <div class="font-weight-bold">{{ $item['title'] }}</div>
                                        @if ($item['detail']) <small class="text-muted">{{ $item['detail'] }}</small> @endif
                                    </div>
                                    @if ($item['url']) <i class="fas fa-chevron-right text-muted"></i> @endif
                                @if ($item['url'])</a>@else</div>@endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>
</div>
@endsection
