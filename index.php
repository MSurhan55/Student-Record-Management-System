<?php include 'config.php'; ?>

<?php
// Pagination & Search Logic
$limit = 5; // Records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = $search ? "WHERE studentname LIKE '%$search%' OR email LIKE '%$search%'" : '';

$result = $conn->query("SELECT COUNT(*) as total FROM students $search_query");
$total = $result->fetch_assoc()['total'];
$pages = ceil($total / $limit);

$students = $conn->query("SELECT * FROM students $search_query LIMIT $start, $limit");

// Insert, Update, Delete Logic (same as before)...

// Insert
if (isset($_POST['submit'])) {
    $studentname = $_POST['studentname'];
    $cnic = $_POST['cnic'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $semester = $_POST['semester'];

    $conn->query("INSERT INTO students (studentname, cnic, email, department, age, gender, semester)
                 VALUES ('$studentname', '$cnic', '$email', '$department', $age, '$gender', '$semester')");
    header("Location: index.php");
}

// Delete
if (isset($_GET['delete'])) {
    $sno = $_GET['delete'];
    $conn->query("DELETE FROM students WHERE sno = $sno");
    header("Location: index.php");
}

// Update
if (isset($_POST['update'])) {
    $sno = $_POST['sno'];
    $studentname = $_POST['studentname'];
    $cnic = $_POST['cnic'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $semester = $_POST['semester'];

    $conn->query("UPDATE students SET 
        studentname='$studentname',
        cnic='$cnic',
        email='$email',
        department='$department',
        age=$age,
        gender='$gender',
        semester='$semester'
        WHERE sno=$sno");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student CRUD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">

<div class="container">
  <h2 class="text-center mb-4">Student Records - PHP CRUD (Bootstrap + Pagination + Search)</h2>

  <form method="GET" class="mb-3 d-flex">
    <input type="text" name="search" class="form-control me-2" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-primary">Search</button>
  </form>

  <form method="POST" class="row g-3">
    <input type="hidden" name="sno" id="sno">
    <div class="col-md-6"><input type="text" name="studentname" id="studentname" class="form-control" placeholder="Student Name" required></div>
    <div class="col-md-6"><input type="text" name="cnic" id="cnic" class="form-control" placeholder="CNIC" required></div>
    <div class="col-md-6"><input type="email" name="email" id="email" class="form-control" placeholder="Email" required></div>
    <div class="col-md-6"><input type="text" name="department" id="department" class="form-control" placeholder="Department" required></div>
    <div class="col-md-3"><input type="number" name="age" id="age" class="form-control" placeholder="Age" required></div>
    <div class="col-md-3">
      <select name="gender" id="gender" class="form-select" required>
        <option value="">Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
    </div>
    <div class="col-md-3"><input type="text" name="semester" id="semester" class="form-control" placeholder="Semester" required></div>
    <div class="col-md-3">
      <button type="submit" name="submit" class="btn btn-success w-100" id="submitBtn">Add</button>
      <button type="submit" name="update" class="btn btn-warning w-100 d-none" id="updateBtn">Update</button>
    </div>
  </form>

  <table class="table table-bordered table-striped mt-4">
    <thead class="table-dark">
      <tr>
        <th>S.No</th>
        <th>Name</th>
        <th>CNIC</th>
        <th>Email</th>
        <th>Department</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Semester</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $students->fetch_assoc()): ?>
      <tr>
        <td><?= $row['sno'] ?></td>
        <td><?= $row['studentname'] ?></td>
        <td><?= $row['cnic'] ?></td>
        <td><?= $row['email'] ?></td>
        <td><?= $row['department'] ?></td>
        <td><?= $row['age'] ?></td>
        <td><?= $row['gender'] ?></td>
        <td><?= $row['semester'] ?></td>
        <td>
          <button class="btn btn-sm btn-primary" onclick='editRecord(<?= json_encode($row) ?>)'>Edit</button>
          <a href="?delete=<?= $row['sno'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<script>
  function editRecord(data) {
    document.getElementById('sno').value = data.sno;
    document.getElementById('studentname').value = data.studentname;
    document.getElementById('cnic').value = data.cnic;
    document.getElementById('email').value = data.email;
    document.getElementById('department').value = data.department;
    document.getElementById('age').value = data.age;
    document.getElementById('gender').value = data.gender;
    document.getElementById('semester').value = data.semester;

    document.getElementById('submitBtn').classList.add('d-none');
    document.getElementById('updateBtn').classList.remove('d-none');
  }
</script>

</body>
</html>
