document.addEventListener("DOMContentLoaded", () => {

    if (typeof DataTable === "undefined") {
        console.error("DataTables library not loaded.");
        return;
    }

    const topPagesData = JSON.parse(
        document.getElementById("topPagesData").textContent
    );

    console.log(topPagesData);

    new DataTable("#aaTopPagesTable", {
        data: topPagesData,

        columns: [
            {
                data: null,
                title: "Page",
                render: function (data) {
                    return data.page_title || ("Page #" + data.page_uid);
                }
            },
            {
                data: "total",
                title: "Total Visits"
            },
            {
                data: "visit_date",
                title: "Visits Date"
            }
        ],

        pageLength: 10,
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        order: [[1, "desc"]],

        language: {
            search: "Search:",
            emptyTable: "No page data available",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries"
        }
    });

});