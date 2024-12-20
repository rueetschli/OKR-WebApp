// api.js
// Stellt Funktionen für den AJAX/Fetch-Zugriff auf die API-Endpunkte bereit

const API_BASE = 'api/';

/**
 * Ruft alle verfügbaren Workspaces ab.
 * Gibt ein Promise zurück, das bei Erfolg {success: true, workspaces: [...] } liefert.
 */
function listWorkspaces() {
    return fetch(API_BASE + 'list_workspaces.php')
        .then(response => response.json())
        .catch(err => ({success: false, error: err.message}));
}

/**
 * Erstellt einen neuen Workspace.
 * @param {String} name - Name des Workspaces
 * @param {String} [password=''] - Optionales Passwort
 * Gibt ein Promise zurück, das bei Erfolg {success:true, workspace:{...}} liefert.
 */
function createWorkspace(name, password = '') {
    const formData = new FormData();
    formData.append('name', name);
    if (password.trim() !== '') {
        formData.append('password', password);
    }

    return fetch(API_BASE + 'create_workspace.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .catch(err => ({success: false, error: err.message}));
}

/**
 * Authentifiziert einen geschützten Workspace.
 * @param {String} name - Workspace-Name
 * @param {String} password - Passwort
 */
function authenticateWorkspace(name, password) {
    const formData = new FormData();
    formData.append('name', name);
    formData.append('password', password);

    return fetch(API_BASE + 'authenticate_workspace.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .catch(err => ({success: false, error: err.message}));
}

/**
 * Lädt den Inhalt eines Workspaces.
 * @param {String} name - Name des Workspaces
 * Gibt bei Erfolg {success:true, workspace_data:{...}} zurück.
 */
function loadWorkspaceData(name) {
    const url = `${API_BASE}load_workspace.php?name=${encodeURIComponent(name)}`;
    return fetch(url)
        .then(res => res.json())
        .catch(err => ({success: false, error: err.message}));
}

/**
 * Speichert Workspace-Daten.
 * @param {String} name - Name des Workspaces
 * @param {Object} workspaceData - Updated JSON-Daten
 * Gibt {success:true} oder {success:false, error:...} zurück.
 */
function saveWorkspaceData(name, workspaceData) {
    const formData = new FormData();
    formData.append('name', name);
    formData.append('workspace_data', JSON.stringify(workspaceData));

    return fetch(API_BASE + 'save_workspace_data.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .catch(err => ({success:false, error: err.message}));
}

// Globale Zuweisung, damit app.js die Funktionen nutzen kann
window.listWorkspaces = listWorkspaces;
window.createWorkspace = createWorkspace;
window.authenticateWorkspace = authenticateWorkspace;
window.loadWorkspaceData = loadWorkspaceData;
window.saveWorkspaceData = saveWorkspaceData;
