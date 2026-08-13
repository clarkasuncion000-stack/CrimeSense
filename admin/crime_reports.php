<?php
session_start();
require_once("../configuration/database.php");
require_once("../configuration/session.php");
?>

<!DOCTYPE html>
<html lang="en">

<?php include("../includes/header.php"); ?>

<body>

<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-file-earmark-text"></i> Crime Reports</h3>

        <a href="add_crime.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Crime Report
        </a>
    </div>

    <div class="card shadow">

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Crime Type</th>
                        <th>Barangay</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

<?php

$sql = "SELECT
            c.crimeID,
            c.date_committed,
            c.time_committed,
            c.status,
            ct.crime_name,
            b.barangay_name
        FROM crime_reports c
        INNER JOIN crime_types ct
            ON c.crimeTypeID = ct.crimeTypeID
        INNER JOIN barangays b
            ON c.barangayID = b.barangayID
        ORDER BY c.date_committed DESC, c.time_committed DESC";

$result = mysqli_query($conn,$sql);

while($row=mysqli_fetch_assoc($result))
{
?>

<tr>

<td><?= $row['crimeID']; ?></td>

<td><?= date("F d, Y",strtotime($row['date_committed'])); ?></td>

<td><?= date("h:i A",strtotime($row['time_committed'])); ?></td>

<td><?= $row['crime_name']; ?></td>

<td><?= $row['barangay_name']; ?></td>

<td>

<?php

if($row['status']=="Open")
{
    echo "<span class='badge bg-danger'>Open</span>";
}
elseif($row['status']=="Solved")
{
    echo "<span class='badge bg-success'>Solved</span>";
}
else
{
    echo "<span class='badge bg-secondary'>Closed</span>";
}

?>

</td>

<td>

<a href="view_crime.php?id=<?= $row['crimeID']; ?>" class="btn btn-info btn-sm">
View
</a>

<a href="edit_crime.php?id=<?= $row['crimeID']; ?>" class="btn btn-warning btn-sm">
Edit
</a>

<a href="delete_crime.php?id=<?= $row['crimeID']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this report?');">
Delete
</a>

</td>

</tr>

<?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include("../includes/footer.php"); ?>

</body>
</html>