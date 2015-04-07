$(document).ready(function() {
    updateManagers();
    updateWorkers();
});

function updateManagers() {
    $.getJSON("/managers/json", function (data) {
        renderManagers(data);

        setTimeout(updateManagers, 1000);
    });
}

function updateWorkers() {
    $.getJSON("/workers/json", function (data) {
        renderWorkers(data);

        setTimeout(updateWorkers, 1000);
    });
}

function renderManagers(data) {
    var tbody = '';
    $.each(data, function (key, row) {
        tbody += "<tr>" +
        "<th scope='row'>" + row.id + "</th>" +
            "<td>" + row.countWorkers + "</td>" +
            "<td>" + row.lastActivity+ "</td>" +
            "<td>" +
                "<a href=\"/manager/sub/" + row.id + "/1\" class=\"btn btn-warning\">Sub</a> " +
                "<a href=\"/manager/add/" + row.id + "/1\" class=\"btn btn-success\">Add</a>" +
            "</td>" +
        "</tr>";
    });

    $("#managers tbody").html(tbody);
}

function renderWorkers(data) {
    var tbody = '';
    $.each(data, function (key, row) {
        tbody += "<tr>" +
        "<th scope='row'>" + row.id + "</th>" +
            "<td>" + row.status + "</td>" +
            "<td>" + row.lastActiveTime + "</td>" +
            "<td>" + row.countRuns + "</td>" +
            "<td>" + row.task + "</td>" +
            "<td>" +
                "<a href=\"/stop/" + row.id + "\" class=\"btn btn-warning\">Stop</a>" +
                "<a href=\"/start/" + row.id + "\" class=\"btn btn-success\">Start</a>" +
            "</td>" +
        "</tr>";
    });

    $("#workers tbody").html(tbody);
}
