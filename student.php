<?php
require 'db.php';

class student {
    private $db;
    public function __construct() {
        $this->db = (new Database())->connect();
    }
    public function all(){
        return $this->db->query("SELECT * FROM students ORDER BY id DESC");
    }
    public function store($name, $course){
        $stmt = $this->db->prepare("INSERT INTO students (fullname, course) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $course);
        return $stmt->execute();
    }
    public function update($id, $name, $course){
        $stmt = $this->db->prepare("UPDATE students SET fullname = ?, course = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $course, $id);
        return $stmt->execute();
    }
    public function delete($id){
        $stmt = $this->db->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

}