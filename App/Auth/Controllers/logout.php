<?php
 session_start();
        session_destroy();
        header('Location: /LuneraHotel/App/Public');
        exit;
?>