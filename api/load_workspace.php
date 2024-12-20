<?php
// load_workspace.php
require_once 'utils.php';

$name = isset($_GET['name']) ? trim($_GET['name']) : '';
if ($name === '') {
    json_response(["success" => false, "error" => "Workspace-Name erforderlich."]);
}

$globalData = load_global_data();
if (!$globalData) {
    json_response(["success" => false, "error" => "Keine globalen Daten vorhanden."]);
}

$workspace = find_workspace_entry($globalData, $name);
if (!$workspace) {
    json_response(["success" => false, "error" => "Workspace nicht gefunden."]);
}

$filepath = WORKSPACES_DIR . $workspace['file'];
$data = read_json_file($filepath);
if ($data === null) {
    json_response(["success" => false, "error" => "Konnte Workspace-Datei nicht lesen."]);
}

json_response(["success" => true, "workspace_data" => $data]);
