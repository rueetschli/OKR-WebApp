<?php
// is_authenticated_workspace.php
// Gibt Auskunft, ob ein Workspace geschützt ist und ob der User authentifiziert ist.
require_once 'utils.php';

$name = isset($_GET['name']) ? trim($_GET['name']) : '';
if ($name === '') {
    json_response(["success"=>false,"error"=>"Workspace-Name erforderlich."]);
}

$globalData = load_global_data();
if (!$globalData) {
    json_response(["success"=>false,"error"=>"Keine globalen Daten vorhanden."]);
}

$ws = find_workspace_entry($globalData, $name);
if (!$ws) {
    json_response(["success"=>false,"error"=>"Workspace nicht gefunden."]);
}

$protected = isset($ws['protected']) && $ws['protected'] === true;
$authenticated = !$protected || is_authenticated($name);

json_response([
    "success" => true,
    "protected" => $protected,
    "authenticated" => $authenticated
]);
