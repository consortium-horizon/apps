

$(document).ready(function() {
    var mData = JSON.parse( dataSet );
    //DataTables


        // Implement DataTables
    var table = $('#papTop5Tbl').DataTable({
        responsive: true,
        searching: false,
        paging: false,
        info:false,
        pageLength: 5,
        data: mData,
        order: [[1, "desc"]],
        columns: [
            {
                title: "Member",
                data: "name"
            }, {
                title: "Participation",
                data: "paps"
            }
        ]

    });
    var table = $('#papBot5Tbl').DataTable({
        responsive: true,
        searching: false,
        paging: false,
        info:false,
        pageLength: 5,
        data: mData,
        order: [[1, "asc"]],
        columns: [
            {
                title: "Member",
                data: "name"
            }, {
                title: "Participation",
                data: "paps"
            }
        ]

    });
});