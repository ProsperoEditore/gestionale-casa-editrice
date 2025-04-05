<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Test DataTables</title>

    <!-- ✅ DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- ✅ jQuery PRIMA -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ✅ DataTables DOPO jQuery -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style>
        body { padding: 2rem; font-family: sans-serif; }
    </style>
</head>
<body>

    <h2>Tabella di test con DataTables</h2>

    <table id="giacenzeTable" class="display">
        <thead>
            <tr>
                <th>Marchio</th>
                <th>ISBN</th>
                <th>Titolo</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Prospero</td><td>9788831304166</td><td>Il maschilismo orecchiabile</td></tr>
            <tr><td>Prospero</td><td>9791281091421</td><td>Una questione di sangue</td></tr>
            <tr><td>Altro</td><td>9780000000001</td><td>Altro libro</td></tr>
        </tbody>
    </table>

    <script>
        $(document).ready(function () {
            console.log("✅ jQuery:", $.fn.jquery);
            console.log("✅ DataTables disponibile?", typeof $.fn.dataTable);

            $('#giacenzeTable').DataTable({
                paging: false,
                ordering: true,
                info: false
            });
        });
    </script>

</body>
</html>
