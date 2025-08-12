<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database.php';
require_once 'database.php';

$search = isset($_GET['search']) ? $_GET['search'] : "";
$query = "SELECT * FROM buku WHERE judul LIKE :search";
$stmt = $conn->prepare($query);
$searchTerm = "%".$search."%";
$stmt->bindParam(":search", $searchTerm, PDO::PARAM_STR);
$stmt->execute();

// Tampilkan data dalam bentuk tabel
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<div class='table-row'>
            <div class='table-cell'>{$row['id']}</div>
            <div class='table-cell'><a href='buku.php?id={$row['id']}'>{$row['judul']}</a></div>
            <div class='table-cell'>{$row['penulis']}</div>
            <div class='table-cell'>{$row['penerbit']}</div>
            <div class='action-cell'>
              <span class='action-link'><a href='editBuku.php?id={$row['id']}'>EDIT</a></span>
              <span>|</span>
              <span class='action-link'><a href='#' onclick='deleteBook({$row['id']})'>HAPUS</a></span>
            </div>
          </div>";
}
?>
