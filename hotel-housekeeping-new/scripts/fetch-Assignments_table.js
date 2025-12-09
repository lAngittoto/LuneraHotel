function loadTable() {
    fetch("./includes/fetch-assignmentsTable.php")
        .then(res => res.text())
        .then(data => {
            document.getElementById("AssignmentsTableContainer").innerHTML = data;
        })
        .catch(err => console.error("Error loading table:", err));
}

// Expose function globally for reload calls
window.loadTable = loadTable;

loadTable();

