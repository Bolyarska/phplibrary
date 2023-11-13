<?php

date_default_timezone_set('Europe/Helsinki');

$host = 'localhost';
$dbname = 'yii2advanced';
$user = 'betina';
$password = 'Backspace1234!';

try {
    // instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

    // exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Select saved books
    $sql = "SELECT book_id, SUM(book_quantity) AS total_quantity FROM savedbooks WHERE expiration_time < NOW() GROUP BY book_id";
    $stmt = $pdo->query($sql);
    $saved_books_array = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($saved_books_array) . "\n";

    // Iterate through saved books,update number_available
    foreach ($saved_books_array as $saved_book) {
        $book_id = $saved_book['book_id'];
        $total_quantity = $saved_book['total_quantity'];

        echo json_encode($total_quantity) . "\n";

        // Get the current number_available for the book
        $selectSql = "SELECT number_available FROM books WHERE id = :book_id";
        $selectStmt = $pdo->prepare($selectSql);
        $selectStmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $selectStmt->execute();
        $current_quantity = $selectStmt->fetchColumn();

        // Calculate the new number_available by adding the total quantity
        $new_quantity = $current_quantity + $total_quantity;

        // Update the number_available in books table
        $updateSql = "UPDATE books SET number_available = :new_quantity WHERE id = :book_id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':new_quantity', $new_quantity, PDO::PARAM_INT);
        $updateStmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $updateStmt->execute();
    }

    $sql = "DELETE FROM savedbooks WHERE expiration_time < NOW()";

    // Execute
    $pdo->exec($sql);

    echo "Expired records deleted.\n";
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}