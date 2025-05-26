<?php
function fetchColumnById($pdo, $query, $id)
{
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
