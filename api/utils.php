<?php
// utils.php
// Gemeinsame Hilfsfunktionen für die API-Endpunkte

// Session für Authentifizierung starten
session_start();

// Konstanten für Datenpfade
define('GLOBAL_DATA_PATH', __DIR__ . '/../data/global.json');
define('WORKSPACES_DIR', __DIR__ . '/../data/workspaces/');

/**
 * Liest eine JSON-Datei und gibt sie als Array zurück.
 * Gibt null zurück, wenn die Datei nicht existiert oder fehlerhaft ist.
 */
function read_json_file($path) {
    if (!file_exists($path)) {
        return null;
    }
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    return $data;
}

/**
 * Schreibt ein Array als JSON in eine Datei.
 * Gibt true zurück, wenn erfolgreich, sonst false.
 */
function write_json_file($path, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($path, $json) !== false;
}

/**
 * Lädt die globalen Daten (global.json).
 * Gibt ein Array zurück oder null, wenn nicht vorhanden.
 */
function load_global_data() {
    return read_json_file(GLOBAL_DATA_PATH);
}

/**
 * Speichert die globalen Daten.
 */
function save_global_data($data) {
    return write_json_file(GLOBAL_DATA_PATH, $data);
}

/**
 * Erstellt einen sicheren Passwort-Hash.
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Prüft ein Passwort gegen einen Hash.
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sucht in globalen Daten nach einem bestimmten Workspace-Eintrag anhand des Namens.
 * Gibt den Workspace-Eintrag als Array zurück oder null, wenn nicht gefunden.
 */
function find_workspace_entry($globalData, $workspaceName) {
    if (!isset($globalData['workspaces']) || !is_array($globalData['workspaces'])) {
        return null;
    }
    foreach ($globalData['workspaces'] as $ws) {
        if (isset($ws['name']) && $ws['name'] === $workspaceName) {
            return $ws;
        }
    }
    return null;
}

/**
 * Gibt eine JSON-Antwort zurück und beendet das Skript.
 * @param array $data Array mit success/error Feldern.
 */
function json_response($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Prüft, ob der aktuelle Nutzer für einen bestimmten Workspace authentifiziert ist.
 * Wenn der Workspace nicht geschützt ist, ist Authentifizierung nicht nötig.
 * Wenn geschützt, muss der Workspace in der Session-Whitelist sein.
 */
function is_authenticated($workspaceName) {
    // Wenn keine Session-Liste vorhanden, false
    if (!isset($_SESSION['authenticated_workspaces']) || !is_array($_SESSION['authenticated_workspaces'])) {
        return false;
    }
    return in_array($workspaceName, $_SESSION['authenticated_workspaces']);
}

/**
 * Markiert einen Workspace als authentifiziert in der aktuellen Session.
 */
function authenticate_session($workspaceName) {
    if (!isset($_SESSION['authenticated_workspaces'])) {
        $_SESSION['authenticated_workspaces'] = [];
    }
    if (!in_array($workspaceName, $_SESSION['authenticated_workspaces'])) {
        $_SESSION['authenticated_workspaces'][] = $workspaceName;
    }
}

/**
 * Lädt die Daten eines Workspaces (JSON) als Array.
 * Gibt null zurück, wenn die Datei nicht existiert oder Fehler auftritt.
 */
function load_workspace_data($workspaceName, $globalData) {
    $entry = find_workspace_entry($globalData, $workspaceName);
    if (!$entry || !isset($entry['file'])) return null;

    $path = WORKSPACES_DIR . $entry['file'];
    return read_json_file($path);
}

/**
 * Speichert die Daten eines Workspaces.
 * Gibt true bei Erfolg, false bei Fehler zurück.
 */
function save_workspace_data($workspaceName, $globalData, $workspaceData) {
    $entry = find_workspace_entry($globalData, $workspaceName);
    if (!$entry || !isset($entry['file'])) return false;

    $path = WORKSPACES_DIR . $entry['file'];
    return write_json_file($path, $workspaceData);
}
