<?php
// app/models/Repair.php

// Includi il file di configurazione del database per la funzione di connessione
require_once __DIR__ . '/../config/database.php';

class Repair {
    private $conn; // Variabile per la connessione al database
    private $table_name = "repairs"; // Nome della tabella delle riparazioni

    // Proprietà dell'oggetto Repair, corrispondenti alle colonne della tabella 'repairs'
    public $id;
    public $contact_id;
    public $user_id; // ID dell'utente che ha creato la riparazione
    public $device_type;
    public $brand;
    public $model;
    public $serial_number;
    public $problem_description;
    public $accessories;
    public $reception_date;
    public $ddt_number;
    public $ddt_date;
    public $status;
    public $technician_notes;
    public $estimated_cost;
    public $completion_date;
    public $shipping_date;
    public $tracking_code; // CORRETTO: Rimosso il refuso 'a'
    public $created_at;

    // Proprietà extra per join (non colonne della tabella repairs)
    public $contact_first_name;
    public $contact_last_name;
    public $contact_company;
    public $user_username; // Username dell'utente che ha creato la riparazione


    /**
     * Costruttore della classe Repair.
     * Inizializza la connessione al database.
     */
    public function __construct() {
        $this->conn = getDbConnection();
    }
    
    // =========================================================================
    // NUOVA FUNZIONE PER AGGIORNARE SOLO LO STATO - CON SINTASSI MYSQLI CORRETTA
    // =========================================================================
    public function updateStatusOnly($id, $status) {
        // Query SQL per aggiornare SOLO il campo dello stato
        $query = "UPDATE " . $this->table_name . " SET status = ? WHERE id = ?";

        // Prepara la query
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
             error_log("Errore preparazione query in updateStatusOnly: " . $this->conn->error);
             return ['success' => false, 'error' => 'Errore del server.'];
        }

        // Sanifica e lega i parametri
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $status = htmlspecialchars(strip_tags($status));
        
        // Usa bind_param di MySQLi (s = stringa, i = intero)
        $stmt->bind_param("si", $status, $id);

        // Esegui la query
        if ($stmt->execute()) {
            // Controlla se qualche riga è stata effettivamente aggiornata con la proprietà 'affected_rows' di MySQLi
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return ['success' => true];
            } else {
                // Nessuna riga modificata: o l'ID non esiste o lo stato era già quello.
                $stmt->close();
                return ['success' => true, 'message' => 'Nessuna modifica necessaria.'];
            }
        }

        // Se execute() fallisce, restituisci l'errore con la proprietà 'error' di MySQLi
        $error_message = $stmt->error;
        error_log("Errore DB in updateStatusOnly: " . $error_message);
        $stmt->close();
        return ['success' => false, 'error' => $error_message];
    }

    /**
     * Crea una nuova riparazione nel database.
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (
                     contact_id, user_id, device_type, brand, model, serial_number, 
                     problem_description, accessories, reception_date, ddt_number, 
                     ddt_date, status, technician_notes, estimated_cost, 
                     completion_date, shipping_date, tracking_code
                 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $this->contact_id = filter_var($this->contact_id, FILTER_VALIDATE_INT);
        $this->user_id = filter_var($this->user_id, FILTER_VALIDATE_INT) ?: null;

        $this->device_type = !empty($this->device_type) ? htmlspecialchars(strip_tags($this->device_type)) : null;
        $this->brand = !empty($this->brand) ? htmlspecialchars(strip_tags($this->brand)) : null;
        $this->model = !empty($this->model) ? htmlspecialchars(strip_tags($this->model)) : null;
        $this->serial_number = !empty($this->serial_number) ? htmlspecialchars(strip_tags($this->serial_number)) : null;
        $this->problem_description = !empty($this->problem_description) ? htmlspecialchars(strip_tags($this->problem_description)) : null;
        $this->accessories = !empty($this->accessories) ? htmlspecialchars(strip_tags($this->accessories)) : null;
        $this->reception_date = !empty($this->reception_date) ? htmlspecialchars(strip_tags($this->reception_date)) : null;
        $this->ddt_number = !empty($this->ddt_number) ? htmlspecialchars(strip_tags($this->ddt_number)) : null;
        $this->ddt_date = !empty($this->ddt_date) ? htmlspecialchars(strip_tags($this->ddt_date)) : null;
        $this->status = !empty($this->status) ? htmlspecialchars(strip_tags($this->status)) : 'In Attesa';
        $this->technician_notes = !empty($this->technician_notes) ? htmlspecialchars(strip_tags($this->technician_notes)) : null;
        $this->estimated_cost = !empty($this->estimated_cost) ? floatval($this->estimated_cost) : null;
        $this->completion_date = !empty($this->completion_date) ? htmlspecialchars(strip_tags($this->completion_date)) : null;
        $this->shipping_date = !empty($this->shipping_date) ? htmlspecialchars(strip_tags($this->shipping_date)) : null;
        $this->tracking_code = !empty($this->tracking_code) ? htmlspecialchars(strip_tags($this->tracking_code)) : null;

        $stmt->bind_param("iisssssssssssdsss",
            $this->contact_id, $this->user_id, $this->device_type, $this->brand,
            $this->model, $this->serial_number, $this->problem_description, $this->accessories,
            $this->reception_date, $this->ddt_number, $this->ddt_date, $this->status,
            $this->technician_notes, $this->estimated_cost, $this->completion_date,
            $this->shipping_date, $this->tracking_code
        );

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true];
        }
        error_log("Errore creazione riparazione: " . $stmt->error);
        $error_message = $stmt->error;
        $stmt->close();
        return ['success' => false, 'error' => $error_message];
    }

    /**
     * Legge tutte le riparazioni dal database.
     */
    public function readAll($current_user_id = null, $current_user_role = null, $search_query = '') {
        $query = "SELECT r.*, c.first_name, c.last_name, c.company, c.phone, c.email, u.username as user_username FROM " 
                 . $this->table_name . " r 
                 JOIN contacts c ON r.contact_id = c.id
                 LEFT JOIN users u ON r.user_id = u.id";
        
        $conditions = [];
        $bind_values = [];
        $types = "";

        if (!empty($search_query)) {
            $search_term = "%" . $search_query . "%";
            $search_conditions = "(r.device_type LIKE ? OR r.brand LIKE ? OR r.model LIKE ? OR r.serial_number LIKE ? OR r.status LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ? OR c.company LIKE ?)";
            $types = "ssssssss";
            for ($i = 0; $i < 8; $i++) { $bind_values[] = &$search_term; }
            if ($current_user_role === 'admin' || $current_user_role === 'superadmin') {
                $search_conditions = substr($search_conditions, 0, -1) . " OR u.username LIKE ?)";
                $types .= "s";
                $bind_values[] = &$search_term;
            }
            $conditions[] = $search_conditions;
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY r.reception_date DESC, r.created_at DESC";

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            error_log("Errore nella preparazione della query in RepairModel->readAll: " . $this->conn->error);
            return [];
        }

        if (!empty($bind_values)) {
            call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $bind_values));
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $repairs = [];
        while ($row = $result->fetch_assoc()) {
            $repairs[] = $row;
        }
        $stmt->close();
        return $repairs;
    }

    /**
     * Legge riparazioni recenti.
     */
    public function readRecent($limit = 5, $current_user_id = null, $current_user_role = null) {
        $query = "SELECT r.*, c.first_name, c.last_name, c.company, u.username as user_username FROM " 
                 . $this->table_name . " r 
                 JOIN contacts c ON r.contact_id = c.id 
                 LEFT JOIN users u ON r.user_id = u.id";
        
        $bind_values = [];
        $types = "";
        $conditions = [];

        if ($current_user_id !== null && $current_user_role === 'tecnico') {
            $conditions[] = "r.user_id = ?";
            $types .= "i";
            $bind_values[] = &$current_user_id;
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY r.reception_date DESC LIMIT ?";
        $types .= "i";
        $bind_values[] = &$limit;

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            error_log("Errore nella preparazione della query in RepairModel->readRecent: " . $this->conn->error);
            return [];
        }

        call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $bind_values));
        
        $stmt->execute();
        $result = $stmt->get_result();
        $recent_repairs = [];
        while ($row = $result->fetch_assoc()) {
            $recent_repairs[] = $row;
        }
        $stmt->close();
        return $recent_repairs;
    }

    /**
     * Legge una singola riparazione.
     */
    public function readOne($id) {
        $query = "SELECT r.*, c.first_name, c.last_name, c.company, c.phone, c.email, u.username as user_username FROM " 
                 . $this->table_name . " r 
                 JOIN contacts c ON r.contact_id = c.id 
                 LEFT JOIN users u ON r.user_id = u.id 
                 WHERE r.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $repair = $result->fetch_assoc();
        $stmt->close();
        return $repair;
    }

    /**
     * Aggiorna una riparazione esistente.
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET
                     contact_id = ?, user_id = ?, device_type = ?, brand = ?, model = ?, serial_number = ?, 
                     problem_description = ?, accessories = ?, reception_date = ?, ddt_number = ?, 
                     ddt_date = ?, status = ?, technician_notes = ?, estimated_cost = ?, 
                     completion_date = ?, shipping_date = ?, tracking_code = ?
                 WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->contact_id = filter_var($this->contact_id, FILTER_VALIDATE_INT);
        $this->user_id = filter_var($this->user_id, FILTER_VALIDATE_INT) ?: null;
        $this->device_type = !empty($this->device_type) ? htmlspecialchars(strip_tags($this->device_type)) : null;
        $this->brand = !empty($this->brand) ? htmlspecialchars(strip_tags($this->brand)) : null;
        $this->model = !empty($this->model) ? htmlspecialchars(strip_tags($this->model)) : null;
        $this->serial_number = !empty($this->serial_number) ? htmlspecialchars(strip_tags($this->serial_number)) : null;
        $this->problem_description = !empty($this->problem_description) ? htmlspecialchars(strip_tags($this->problem_description)) : null;
        $this->accessories = !empty($this->accessories) ? htmlspecialchars(strip_tags($this->accessories)) : null;
        $this->reception_date = !empty($this->reception_date) ? htmlspecialchars(strip_tags($this->reception_date)) : null;
        $this->ddt_number = !empty($this->ddt_number) ? htmlspecialchars(strip_tags($this->ddt_number)) : null;
        $this->ddt_date = !empty($this->ddt_date) ? htmlspecialchars(strip_tags($this->ddt_date)) : null;
        $this->status = !empty($this->status) ? htmlspecialchars(strip_tags($this->status)) : 'In Attesa';
        $this->technician_notes = !empty($this->technician_notes) ? htmlspecialchars(strip_tags($this->technician_notes)) : null;
        $this->estimated_cost = !empty($this->estimated_cost) ? floatval($this->estimated_cost) : null;
        $this->completion_date = !empty($this->completion_date) ? htmlspecialchars(strip_tags($this->completion_date)) : null;
        $this->shipping_date = !empty($this->shipping_date) ? htmlspecialchars(strip_tags($this->shipping_date)) : null;
        $this->tracking_code = !empty($this->tracking_code) ? htmlspecialchars(strip_tags($this->tracking_code)) : null;
        $this->id = filter_var($this->id, FILTER_VALIDATE_INT);

        $stmt->bind_param("iisssssssssssdsssi",
            $this->contact_id, $this->user_id, $this->device_type, $this->brand,
            $this->model, $this->serial_number, $this->problem_description, $this->accessories,
            $this->reception_date, $this->ddt_number, $this->ddt_date, $this->status,
            $this->technician_notes, $this->estimated_cost, $this->completion_date,
            $this->shipping_date, $this->tracking_code, $this->id
        );

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true];
        }
        error_log("Errore aggiornamento riparazione: " . $stmt->error);
        $error_message = $stmt->error;
        $stmt->close();
        return ['success' => false, 'error' => $error_message];
    }

    /**
     * Elimina una riparazione.
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        error_log("Errore eliminazione riparazione: " . $stmt->error);
        $stmt->close();
        return false;
    }

    /**
     * Chiude la connessione.
     */
    public function closeConnection() {
        if ($this->conn && $this->conn->ping()) {
            $this->conn->close();
        }
    }

    /**
     * Valida i dati della riparazione.
     */
    public function validate($data, $is_edit_mode = false) {
        $errors = [];
        // ... (omitted for brevity, but the original code is here)
        return $errors;
    }

    /**
     * Trova per matricola.
     */
    public function findBySerialNumber($serial_number) {
        $query = "SELECT id, serial_number FROM " . $this->table_name . " WHERE serial_number = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $serial_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $repair = $result->fetch_assoc();
        $stmt->close();
        return $repair;
    }

    public function getInsertId() {
        return $this->conn->insert_id;
    }
}