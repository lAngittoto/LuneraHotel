<?php
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'available':
            return "bg-green-500 text-white"; 
        case 'booked':
            return "bg-blue-500 text-white"; 
        case 'under maintenance':
            return "bg-yellow-500 text-black"; 
        case 'occupied':
            return "bg-red-500 text-white";
        case 'unavailable':
            return "bg-orange-500 text-white";  
        case 'in progress':
            return "bg-indigo-500 text-black"; 
        default:
            return "bg-gray-500 text-white"; 
    }
}
