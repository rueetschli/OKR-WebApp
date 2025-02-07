<?php
// save_workspace_data.php
require_once 'utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(["success" => false, "error" => "Nur POST-Anfragen erlaubt."]);
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$dataJson = isset($_POST['workspace_data']) ? $_POST['workspace_data'] : '';

if ($name === '' || $dataJson === '') {
    json_response(["success" => false, "error" => "Name und workspace_data sind erforderlich."]);
}

$updatedData = json_decode($dataJson, true);
if ($updatedData === null) {
    json_response(["success" => false, "error" => "Ungültiges JSON für workspace_data."]);
}

$globalData = load_global_data();
if (!$globalData) {
    json_response(["success" => false, "error" => "Keine globalen Daten vorhanden."]);
}

$workspace = find_workspace_entry($globalData, $name);
if (!$workspace) {
    json_response(["success" => false, "error" => "Workspace nicht gefunden."]);
}

// Bei geschütztem Workspace Authentifizierung prüfen
if (isset($workspace['protected']) && $workspace['protected'] === true && !is_authenticated($name)) {
    json_response(["success" => false, "error" => "Zugriff verweigert. Bitte authentifizieren."]);
}

$updatedData['last_update'] = date('Y-m-d');

// Speichern
if (!save_workspace_data($name, $globalData, $updatedData)) {
    json_response(["success" => false, "error" => "Speichern fehlgeschlagen."]);
}

json_response(["success" => true, "message" => "Daten erfolgreich gespeichert."]);
