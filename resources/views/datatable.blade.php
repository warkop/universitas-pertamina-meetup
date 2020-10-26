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
    token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoIiwiaWF0IjoxNjAzNDQxMDk3LCJleHAiOjE2MDM1Mjc0OTcsIm5iZiI6MTYwMzQ0MTA5NywianRpIjoienowc0ZhOHBSdjAyRU11byIsInN1YiI6MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.n05i2mD_2mqiC_FV2CbfrT_JnpkCRM9Ew_ElTaCGWlQ";
    $(document).ready(function() {
        $('#example').DataTable( {
            "processing": true,
            "serverSide": true,

            "ajax": {
                "url": "api/payment?type=1",
                "beforeSend": function (xhr) {
                    xhr.setRequestHeader('Authorization',
                        "Bearer " +token
                    )
                }
            },
            "columns": [
                {data: "name"},
                {data: "institution_name"},
                {data: "department_name"}
            ]
        } );
    } );
</script>
