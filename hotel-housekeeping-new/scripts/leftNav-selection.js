
function disableAll(){
    activate_assignments.style.display = "none";
    activate_assignment_history.style.display = "none";
    activate_staff_admin_dash.style.display = "none";
    activate_staff_list.style.display = "none";
    activate_room_list.style.display = "none";
    activate_reports.style.display = "none";
    activate_maintenance_reports.style.display = "none";
    activate_dashboard.style.display = "none";
}

const linksAdminNav = document.querySelectorAll(".leftNavArea nav ul li a");


const activate_dashboard = document.getElementById("admin-dashboard");
const activate_assignments = document.getElementById("admin-assignments");
const activate_assignment_history = document.getElementById("admin-assignment-history");
const activate_staff_admin_dash = document.getElementById("admin-staff");
const activate_staff_list = document.getElementById("admin-staff-list");
const activate_room_list = document.getElementById("admin-room-list");
const activate_reports = document.getElementById("admin-reports");
const activate_maintenance_reports = document.getElementById("admin-maintenance-reports");


activate_assignments.style.display = "none";
activate_assignment_history.style.display = "none";
activate_staff_admin_dash.style.display = "none";
activate_staff_list.style.display = "none";
activate_room_list.style.display = "none";
activate_reports.style.display = "none";
activate_maintenance_reports.style.display = "none";

linksAdminNav.forEach(link => {
    link.addEventListener("click", (e) =>{
    e.preventDefault();

    linksAdminNav.forEach(l => l.classList.remove("active"));
    link.classList.add("active");

    disableAll();
    if(document.getElementById("dashboard").classList.contains("active")) {
        activate_dashboard.style.display = "block";
    }
    else if(document.getElementById("assignments").classList.contains("active")){
        activate_assignments.style.display = "block";
    }
    else if(document.getElementById("assignment-history").classList.contains("active")){
        activate_assignment_history.style.display = "block";
        if (typeof loadAssignmentHistory === 'function') {
            loadAssignmentHistory();
        }
    }
    else if(document.getElementById("staff").classList.contains("active")){
        activate_staff_admin_dash.style.display = "block";
    }
    else if(document.getElementById("staff-list").classList.contains("active")){
        activate_staff_list.style.display = "block";
        if (typeof loadStaffList === 'function') {
            loadStaffList();
        }
    }
    else if(document.getElementById("room-list").classList.contains("active")){
        activate_room_list.style.display = "block";
        if (typeof loadRoomList === 'function') {
            loadRoomList();
        }
    }
    else if(document.getElementById("reports").classList.contains("active")){
        activate_reports.style.display = "block";
    }
    else if(document.getElementById("maintenance-reports").classList.contains("active")){
        activate_maintenance_reports.style.display = "block";
        if (typeof loadMaintenanceReports === 'function') {
            loadMaintenanceReports();
        }
    }
  })
});