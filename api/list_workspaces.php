<?php
// list_workspaces.php
require_once 'utils.php';

$globalData = load_global_data();
if (!$globalData) {
    json_response(["success" => false, "error" => "Keine globalen Daten gefunden."]);
}

$workspaces = isset($globalData['workspaces']) ? $globalData['workspaces'] : [];
$workspaceList = [];
foreach ($workspaces as $ws) {
    $workspaceList[] = [
        "name" => $ws['name'],
        "protected" => isset($ws['protected']) ? (bool)$ws['protected'] : false
    ];
}

json_response(["success" => true, "workspaces" => $workspaceList]);
