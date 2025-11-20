<?php

function correctGrammar($count) {
    if($count == 1) {
        return "Up to 1 person";
    }else {
         return "Up to {$count} people";
    }
}

function correctGuest($count) {
    if($count ==  1) {
        return "1 Guest";
    }else{
        return "{$count} Guests";
    }
}

function popularity($count) {
    if ($count <= 1) {
        return "Booking";
    } else {
        return "Bookings";
    }
}
