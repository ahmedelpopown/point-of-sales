<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employees Report</title>
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
        <h1>Employee Report</h1>
        <p>Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Position</th>
                <th>Salary</th>
                <th>Hire Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->full_name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->department }}</td>
                    <td>{{ $employee->position }}</td>
                    <td>${{ number_format($employee->salary, 2) }}</td>
                    <td>{{ optional($employee->hire_date)->format('M d, Y') }}</td>
                    <td class="status-{{ $employee->status }}">
                        {{ ucfirst($employee->status) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>