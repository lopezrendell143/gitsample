<?php
require 'student.php';

$student = new student();
    if(isset($_POST['action'])){
        if($_POST['action'] == 'fetch'){
            $data = $student->all();
            foreach ($data as $row){
                echo "
                <tr>
                    <td>{$row['fullname']}</td>
                    <td>{$row['course']}</td>
                    <td>
                    <button class='btn btn-sm btn-warning edit'
                        data-id='{$row['id']}'
                        data-name='{$row['fullname']}'
                        data-course='{$row['course']}'>Edit</button>
                    <button class='btn btn-sm btn-danger delete'
                        data-id='{$row['id']}'>Delete</button>
                    </td>
                </tr>
                ";
            }
            exit;
        }
        elseif($_POST['action'] == 'add'){
            $name = $_POST['fullname'];
            $course = $_POST['course'];
            if($student->store($name, $course)){
                echo json_encode(['success' => true, 'message' => 'Student added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add student']);
            }
            exit;
        }
        // --- NEW: PHP UPDATE LOGIC ---
        elseif($_POST['action'] == 'update'){
            $id = $_POST['id'];
            $name = $_POST['fullname'];
            $course = $_POST['course'];
            if($student->update($id, $name, $course)){ // Assumes update() exists in your student class
                echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update student']);
            }
            exit;
        }
        // --- NEW: PHP DELETE LOGIC ---
        elseif($_POST['action'] == 'delete'){
            $id = $_POST['id'];
            if($student->delete($id)){ // Assumes delete() exists in your student class
                echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete student']);
            }
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1>Student List</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add Student</button>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        </tbody>
                </table>
            </div>
        </div>
    
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addStudentForm">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                        </div>
                        <div class="mb-3">
                            <label for="course" class="form-label">Course</label>
                            <input type="text" class="form-control" id="course" name="course" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStudentForm">
                        <input type="hidden" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_fullname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="edit_fullname" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_course" class="form-label">Course</label>
                            <input type="text" class="form-control" id="edit_course" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Update Student</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fetch and display students on page load
        function fetchStudents() {
            fetch('index.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=fetch'
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('studentTableBody').innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
        }

        // Load students on page load
        document.addEventListener('DOMContentLoaded', fetchStudents);

        // Handle add student form submission
        document.getElementById('addStudentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('fullname', document.getElementById('fullname').value);
            formData.append('course', document.getElementById('course').value);

            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addStudentModal'));
                    modal.hide();
                    document.getElementById('addStudentForm').reset();
                    fetchStudents();
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // --- NEW: EVENT DELEGATION FOR EDIT & DELETE BUTTONS ---
        document.getElementById('studentTableBody').addEventListener('click', function(e) {
            // EDIT ACTION TRIGGERED
            if (e.target.classList.contains('edit')) {
                const btn = e.target;
                
                // Populate the Edit Modal form inputs with data attributes from the row button
                document.getElementById('edit_id').value = btn.getAttribute('data-id');
                document.getElementById('edit_fullname').value = btn.getAttribute('data-name');
                document.getElementById('edit_course').value = btn.getAttribute('data-course');
                
                // Programmatically open the Edit Modal
                const editModal = new bootstrap.Modal(document.getElementById('editStudentModal'));
                editModal.show();
            }

            // DELETE ACTION TRIGGERED
            if (e.target.classList.contains('delete')) {
                const id = e.target.getAttribute('data-id');
                
                if (confirm('Are you sure you want to delete this student?')) {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', id);

                    fetch('index.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fetchStudents(); // Refresh table
                            alert(data.message);
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            }
        });

        // --- NEW: HANDLE EDIT FORM SUBMISSION ---
        document.getElementById('editStudentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'update');
            formData.append('id', document.getElementById('edit_id').value);
            formData.append('fullname', document.getElementById('edit_fullname').value);
            formData.append('course', document.getElementById('edit_course').value);

            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide modal
                    const modalEl = document.getElementById('editStudentModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                    
                    fetchStudents(); // Refresh table
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>