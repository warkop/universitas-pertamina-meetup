<link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">


<table id="example" class="display" style="width:100%">
    <thead>
        <tr>
            <th>name</th>
            <th>user_id</th>
            <th>invoice_id</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>


<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "api/opportunity",
                "beforeSend": function (xhr) {
                    xhr.setRequestHeader('Authorization',
                        "Bearer " + "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoIiwiaWF0IjoxNjAyODI5MjE2LCJleHAiOjE2MDI5MTU2MTYsIm5iZiI6MTYwMjgyOTIxNiwianRpIjoieFlvQ0pNanhmY3hpMVl6UiIsInN1YiI6MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.wzkEPlZipkmmtIkRLgx4yss_KXbZH42BgM6GJQ9zMu4"
                    )
                }
            },
            "columns": [
                {data: "name"},
                {data: "institution_name"},
                {data: "opportunity_type_name"}
            ]
        } );
    } );
</script>
