<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Orders</title>

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

    </style>

</head>

<body>

    <h1>
        Orders
    </h1>

    <table>

        <thead>

            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Employee</th>
                <th>Items</th>
                <th>Total</th>
                <th>Date</th>
            </tr>

        </thead>

        <tbody>

            @foreach($orders as $order)

                <tr>

                    <td>
                        #{{ $order->id }}
                    </td>

                    <td>
                        {{ $order->user?->name ?? 'Walk-in Customer' }}
                    </td>

                    <td>
                        {{ $order->employee?->full_name ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $order->order_items_count }}
                    </td>

                    <td>
                        {{ number_format($order->total, 2) }} EGP
                    </td>

                    <td>
                        {{ $order->created_at?->format('Y-m-d H:i') }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>