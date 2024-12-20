<?php
// index.php
// Zeigt ein Formular zur Erstellung eines neuen Workspaces an
// und bietet einen Button zum Anzeigen einer durchsuchbaren, paginierten Liste von Workspaces.

?>
<!DOCTYPE html>
<html lang="de-CH">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>OKR WebApp – Übersicht</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<main>
    <section class="intro">
        <p>Willkommen in der OKR WebApp. Wähle einen Workspace aus oder erstelle einen neuen.</p>
    </section>

    <section class="actions">
        <h2>Neuen Workspace erstellen</h2>
        <form id="create-workspace-form">
            <label for="workspace_name">Name des Workspaces:</label><br>
            <input type="text" name="workspace_name" id="workspace_name" required><br><br>
            <label for="workspace_password">Passwort (optional):</label><br>
            <input type="password" name="workspace_password" id="workspace_password"><br><br>
            <button type="submit" class="button">Erstellen</button>
        </form>
    </section>

    <section class="workspace-list-wrapper">
        <h2>Arbeitsbereiche</h2>
        <button id="toggle-workspaces" class="button">Arbeitsbereiche anzeigen</button>
        
        <div id="workspace-controls" style="display:none; margin-top:1rem;">
            <label for="workspace-search">Suche:</label>
            <input type="text" id="workspace-search" placeholder="Suchbegriff eingeben...">
        </div>

        <div id="workspace-list" style="display:none; margin-top:1rem;">
            <ul id="workspace-ul"></ul>
            <div id="workspace-pagination" style="margin-top:1rem; display:flex; gap:0.5rem;">
                <button id="prev-page" class="button btn-sm" disabled>Zurück</button>
                <span id="page-info"></span>
                <button id="next-page" class="button btn-sm" disabled>Weiter</button>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>

<script src="assets/js/api.js"></script>
<script src="assets/js/app.js"></script>

</body>
</html>
