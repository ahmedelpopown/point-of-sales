<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-inactive {
            color: gray;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Products Report</h1>
        <p>Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
              
                <th>name</th>
                <th>barcode</th>
                <th>status</th>
                <th>price</th>
                <th>quantity </th>
                <th>description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->barcode }}</td>
                    <td class="status-{{ $product->status }}">
                        {{ ucfirst($product->status) }}
                    </td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->current_quantity }}</td>
                    <td>{{ $product->description }}</td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>