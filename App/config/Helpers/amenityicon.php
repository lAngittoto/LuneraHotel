<?php

function getAmenityIcon($amenity)
{
    $icons = [
        "Wi-Fi" => 'fa-solid fa-wifi',
        "Private Bathroom" => 'fa-solid fa-sink',
        "Toiletries" => 'fa-solid fa-sink',
        "Blanket" => 'fa-solid fa-rug',
        "Air Conditioning" => 'fa-solid fa-wind',
        "Television" => 'fa-solid fa-tv',
        "Pillows" => 'fa-solid fa-mattress-pillow',
        "Wardrobe / desk" => 'fa-solid fa-person',
        "Safety deposit box" => 'fa-solid fa-box',
        "Mini Refrigerator / mini bar" => 'fa-solid fa-building-circle-check',
        "Coffee and tea maker" => 'fa-solid fa-mug-hot',
        "Setting area / table" => 'fa-solid fa-couch',
        "Extra Beds" => 'fa-solid fa-bed',
        "Crib / Baby Cot" => 'fa-solid fa-baby',
        "Microwave / Kitchenette" => 'fa-solid fa-kitchen-set',
        "Dining Table" => 'fa-solid fa-utensils',
        "Sofa Bed" => 'fa-solid fa-couch',
    ];

    return $icons[$amenity] ?? 'fa-solid fa-circle';
}
