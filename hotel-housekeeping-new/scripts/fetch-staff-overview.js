function loadStaffOverview() {
    fetch("./includes/fetch-staff-overview.php")
        .then(res => res.text())
        .then(data => {
            document.getElementById("staffOverviewContainer").innerHTML = data;
        })
        .catch(err => console.error("Error loading staff overview:", err));
}

// Expose function globally for reload calls
window.loadStaffOverview = loadStaffOverview;

// Load on page load
loadStaffOverview();

// Auto-refresh every 5 seconds
setInterval(loadStaffOverview, 5000);
