function loadStaffTable() {
    fetch("./includes/fetch-Staff_table.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("staffTableContainer").innerHTML = data;
        })
        .catch(error => console.error("Error loading staff:", error));
}

// Expose function globally for reload calls
window.loadStaffTable = loadStaffTable;

loadStaffTable();

