<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database.php';

$search = isset($_GET['search']) ? $_GET['search'] : "";
$query = "SELECT * FROM mahasiswa WHERE nama LIKE :search";
$stmt = $conn->prepare($query);
$searchTerm = "%".$search."%";
$stmt->bindParam(":search", $searchTerm, PDO::PARAM_STR);
$stmt->execute();

// Tampilkan data dalam bentuk baris tabel saja (tanpa container)
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "
    <div class='table-row' data-id='{$row['id']}'>
        <div class='table-cell'>{$row['id']}</div>
        <div class='table-cell'><a href='akunUser.php?id={$row['id']}'>{$row['nim']}</a></div>
        <div class='table-cell'><a href='akunUser.php?id={$row['id']}'>{$row['nama']}</a></div>
        <div class='table-cell'>{$row['jenis_kelamin']}</div>
        <div class='table-cell action-cell'>
            <button class='delete-user' data-id='{$row['id']}'>HAPUS</button>
        </div>
    </div>";
}
?>