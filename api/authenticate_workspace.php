<?php
// authenticate_workspace.php
require_once 'utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(["success" => false, "error" => "Nur POST-Anfragen erlaubt."]);
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($name === '' || $password === '') {
    json_response(["success" => false, "error" => "Name und Passwort sind erforderlich."]);
}

$globalData = load_global_data();
if (!$globalData) {
    json_response(["success" => false, "error" => "Keine globalen Daten vorhanden."]);
}

$workspace = find_workspace_entry($globalData, $name);
if (!$workspace) {
    json_response(["success" => false, "error" => "Workspace nicht gefunden."]);
}

if (!isset($workspace['protected']) || $workspace['protected'] === false) {
    // Kein Passwortschutz nötig
    json_response(["success" => true, "message" => "Workspace ist nicht geschützt. Keine Authentifizierung erforderlich."]);
}

// Passwortgeschützt: prüfen
if (!isset($workspace['password_hash'])) {
    json_response(["success" => false, "error" => "Geschützt, aber kein Passwort-Hash gefunden."]);
}

if (verify_password($password, $workspace['password_hash'])) {
    // Session Auth
    authenticate_session($name);
    json_response(["success" => true, "message" => "Authentifizierung erfolgreich."]);
} else {
    json_response(["success" => false, "error" => "Falsches Passwort."]);
}
