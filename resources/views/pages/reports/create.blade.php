@extends('layout.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Create Report</h1>
    <form action="{{ route('reports.store') }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        <div class="mb-4">
            <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Report Type</label>
            <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="sales">Sales Report</option>
                <option value="inventory">Inventory Report</option>
                <option value="debts">Debts Report</option>
            </select>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Generate Report</button>
        </div>
    </form>
</div>
@endsection