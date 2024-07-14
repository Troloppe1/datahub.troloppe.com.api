<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        #routes-table_filter {
            display: none
        }
    </style>
    <title>DataHUB API Routes</title>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">DataHUB API Routes</h1>
        <input type="text" placeholder="Search routes" id="customSearch" class="form-control mb-4">
        <table class="table table-bordered" id="routes-table">
            <thead>
                <tr>
                    <th>Method</th>
                    <th>URI</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($routes as $route)
                    <tr>
                        <td>{{ $route['method'] }}</td>
                        <td>{{ $route['uri'] }}</td>
                        <td>{{ $route['name'] }}</td>
                        <td>{{ $route['action'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#routes-table').DataTable({
            "paging": false,
            // "searching": false
        });
        $("#customSearch").on('keyup', function() {
            table.search(this.value).draw()
        })
    })
</script>
