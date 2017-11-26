$(document).on("ready", function() {
    $("#reportTable").DataTable({
        "autoWidth": false,
        "pageLength": -1,
        "lengthMenu": [ [25, 50, 100, 200, -1], [25, 50, 100, 200, "All"] ]
    });
});