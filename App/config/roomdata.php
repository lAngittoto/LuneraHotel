<?php
require_once __DIR__ . "/Helpers/colorcoding.php";
require_once __DIR__ . "/Helpers/correctgrammar.php";

class Rooms
{
    public $id;
    public $img;
    public $RoomType;
    public $status;
    public $description;
    public $RoomNumber;
    public $people;
    public $floor;

    public function __construct($id, $img, $RoomType, $status, $description, $RoomNumber, $people, $floor)
    {
        $this->id = $id;
        $this->img = $img;
        $this->RoomType = $RoomType;
        $this->status = $status;
        $this->description = $description;
        $this->RoomNumber = $RoomNumber;
        $this->people = $people;
        $this->floor = $floor;
    }

    public function displayRoom()
    {
  
        echo '<div class="bg-[#ffffff] rounded-t-2xl border border-[#b1b1b1] flex flex-col sm:gap-3 select-none h-full  shadow-2xl">';

        echo "<img src='{$this->img}' alt='Room Image' class='rounded-t-2xl w-full sm:h-[400px] object-cover'>";

        echo '<div class="flex flex-row justify-between items-center p-3">';

        echo "<h1 class='lg:text-2xl sm:text-[1.2rem] md: text-[1.6rem] font-bold'>{$this->RoomType}</h1>";
        $statusClass = getStatusClass($this->status);
        echo "<p class='lg:text-[1.1rem] lg:px-5 lg:py-2 md:text-[0.8rem] md:px-4 md:py-1 text-[1rem] px-3 py-1 rounded-4xl  text-white text-center {$statusClass}'>{$this->status}</p>";

        echo "</div>";

        echo "<p class='p-5 sm:text-[1.1rem] md:text-[1.2rem] lg:text-[1.3rem] text-[#333333]'>{$this->description}</p>";

        echo '<div class="flex flex-row justify-between p-3 mt-auto">';
        echo "<p class='lg:text-[1.4rem] sm:text-[1.2rem] md:text-[1.6rem] text-[#333333]'><i class='fa-solid fa-door-closed text-[#800000]'></i>Room: {$this->RoomNumber}</p>";
        echo "<p class='lg:text-[1.4rem] sm:text-[1.2rem] md:text-[1.6rem] text-[#333333]'>
        <i class='fa-regular fa-user text-[#800000]'></i> " . correctGrammar($this->people) . "
      </p>";

        echo "</div>";


if ($_SESSION['user']['role'] === 'admin') {
    $viewLink = "/LuneraHotel/App/Public/viewdetailsadmin?id={$this->id}";
} else {
    $viewLink = "/LuneraHotel/App/Public/viewdetails?id={$this->id}";
}



        echo '<div class="flex justify-center w-full">';
        echo "<a href='{$viewLink}' class='w-full text-center px-5 py-5 bg-[#800000] text-white hover:bg-red-900 transition'>
        View Details <i class='fa-regular fa-file-lines'></i>
      </a>";
        echo "</div>";

        echo "</div>";
    }
}
