<?php
 class Database {

    public function connect(){
        return new mysqli("localhost", "root", "", "gitsample_db");
    }
 }?>