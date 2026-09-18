<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Supplier Report</title>
    <style>
        body {

            font-size: 12px;
            font-family: 'DejaVu Sans', sans-serif;
            direction: ltr;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
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
        <h1>Supplier Report</h1>
   
        <p>Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Governorate</th>
                <th>City</th>
              
                <th>Address</th>
               
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>{{ optional($supplier->governorate)->name }}</td>
                    <td>{{ optional($supplier->city)->name }}</td>
                     
                    <td>{{ $supplier->address }}</td>
                  

                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>