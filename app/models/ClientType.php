<?php
class ClientType {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function getAll() {
        // Esempio base, modifica in base alla tua struttura
        $stmt = $this->conn->prepare("SELECT * FROM client_types ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>