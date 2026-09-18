<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>User Report</title>
    <style>
        body {

            font-size: 12px;
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
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
            text-align: right;
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
        <h1>User Report</h1>
        <p>اختبار عربي</p>
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
                <th>Gender</th>
                <th>Address</th>
                <th>Age</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ optional($user->governorate)->name }}</td>
                    <td>{{ optional($user->city)->name }}</td>
                    <td>{{ $user->gender }}</td>
                    <td>{{ $user->address }}</td>
                    <td>{{ $user->age }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>