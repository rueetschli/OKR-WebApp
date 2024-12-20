<?php
// create_workspace.php
require_once 'utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(["success" => false, "error" => "Nur POST-Anfragen erlaubt."]);
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($name === '') {
    json_response(["success" => false, "error" => "Name des Workspaces ist erforderlich."]);
}

$globalData = load_global_data();
if (!$globalData) {
    $globalData = ["workspaces" => []];
}

// Prüfen, ob Workspace bereits existiert
foreach ($globalData['workspaces'] as $ws) {
    if ($ws['name'] === $name) {
        json_response(["success" => false, "error" => "Ein Workspace mit diesem Namen existiert bereits."]);
    }
}

$filename = strtolower(preg_replace('/\s+/', '_', $name)) . '_data.json';
$filepath = WORKSPACES_DIR . $filename;

$newWorkspace = [
    "name" => $name,
    "file" => $filename,
    "protected" => $password !== ''
];

if ($password !== '') {
    $newWorkspace["password_hash"] = hash_password($password);
}

$globalData['workspaces'][] = $newWorkspace;
if (!save_global_data($globalData)) {
    json_response(["success" => false, "error" => "Fehler beim Speichern der globalen Daten."]);
}

// Leere Workspace-Struktur anlegen
$workspaceData = [
    "objectives" => [],
    "cycles" => [],
    "check_ins" => [],
    "last_update" => date('Y-m-d')
];

if (!write_json_file($filepath, $workspaceData)) {
    json_response(["success" => false, "error" => "Fehler beim Anlegen der Workspace-Datei."]);
}

json_response(["success" => true, "message" => "Workspace erstellt.", "workspace" => $newWorkspace]);
