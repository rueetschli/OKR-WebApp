<?php
// workspace.php
// Seite zum Anzeigen und Bearbeiten eines einzelnen Workspaces.
// Ein geschützter Workspace erfordert ggf. eine Passwort-Eingabe.
// Hier können Objectives, Key Results und Check-Ins verwaltet werden.
$workspaceName = isset($_GET['name']) ? $_GET['name'] : '';
?>
<!DOCTYPE html>
<html lang="de-CH">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>OKR Workspace: <?php echo htmlspecialchars($workspaceName, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

<?php include __DIR__ . '/templates/header.php'; ?>

<main>
    <section class="workspace-detail">
        <h2>Workspace: <?php echo htmlspecialchars($workspaceName, ENT_QUOTES, 'UTF-8'); ?></h2>
        <p id="workspace-info">Lade Daten...</p>
        
        <div id="auth-section" style="display:none;">
            <h3>Passwort erforderlich</h3>
            <form id="auth-form" style="max-width:400px;">
                <label for="auth-password">Passwort eingeben:</label><br>
                <input type="password" id="auth-password" name="auth-password" required style="width:100%;"><br><br>
                <button type="submit" class="button">Authentifizieren</button>
            </form>
        </div>

        <div id="main-content" style="display:none;">
            <h3>Objectives</h3>
            <div id="objectives-container"></div>
            <button class="button" id="btn-add-objective">Objective hinzufügen</button>

            <h3>Check-Ins</h3>
            <div id="checkins-container"></div>
            <button class="button" id="btn-add-checkin">Check-In hinzufügen</button>
        </div>
    </section>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>

<!-- Modale Dialoge für Objectives, KRs und Check-Ins -->

<div id="modal-objective" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Objective bearbeiten/hinzufügen</h3>
        <form id="objective-form">
            <input type="hidden" id="obj_id" value="">
            <label for="obj_title">Titel:</label><br>
            <input type="text" id="obj_title" required style="width:100%;"><br><br>
            <label for="obj_desc">Beschreibung:</label><br>
            <textarea id="obj_desc" style="width:100%;" rows="3"></textarea><br><br>
            <button type="submit" class="button">Speichern</button>
            <button type="button" class="button btn-cancel" data-close-modal="#modal-objective">Abbrechen</button>
        </form>
    </div>
</div>

<div id="modal-kr" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Key Result bearbeiten/hinzufügen</h3>
        <form id="kr-form">
            <input type="hidden" id="kr_obj_id" value="">
            <input type="hidden" id="kr_id" value="">
            <label for="kr_desc">Beschreibung (Value-based KR):</label><br>
            <input type="text" id="kr_desc" required style="width:100%;"><br><br>
            <label for="kr_base">Base Value:</label><br>
            <input type="number" id="kr_base" step="any" style="width:100%;"><br><br>
            <label for="kr_target">Target Value:</label><br>
            <input type="number" id="kr_target" step="any" style="width:100%;"><br><br>
            <label for="kr_current">Current Value:</label><br>
            <input type="number" id="kr_current" step="any" style="width:100%;"><br><br>
            <button type="submit" class="button">Speichern</button>
            <button type="button" class="button btn-cancel" data-close-modal="#modal-kr">Abbrechen</button>
        </form>
    </div>
</div>

<div id="modal-checkin" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Check-In hinzufügen</h3>
        <form id="checkin-form">
            <label for="checkin_notes">Notizen:</label><br>
            <textarea id="checkin_notes" style="width:100%;" rows="3"></textarea><br><br>
            <button type="submit" class="button">Speichern</button>
            <button type="button" class="button btn-cancel" data-close-modal="#modal-checkin">Abbrechen</button>
        </form>
    </div>
</div>

<script src="assets/js/api.js"></script>
<script>
// Dieser Code entspricht der zuvor gezeigten workspace.php-Version
// Keine Veränderungen ausser vielleicht Kommentaren entfernt für Übersicht.
let currentWorkspaceData = null;
const workspaceName = "<?php echo addslashes($workspaceName); ?>";

document.addEventListener('DOMContentLoaded', () => {
    const workspaceInfo = document.getElementById('workspace-info');
    const authSection = document.getElementById('auth-section');
    const mainContent = document.getElementById('main-content');
    const objectivesContainer = document.getElementById('objectives-container');
    const checkinsContainer = document.getElementById('checkins-container');
    const btnAddObjective = document.getElementById('btn-add-objective');
    const btnAddCheckin = document.getElementById('btn-add-checkin');
    const authForm = document.getElementById('auth-form');
    const objectiveForm = document.getElementById('objective-form');
    const krForm = document.getElementById('kr-form');
    const checkinForm = document.getElementById('checkin-form');

    if (!workspaceName) {
        workspaceInfo.textContent = 'Kein Workspace-Name angegeben.';
        return;
    }

    loadWorkspaceData(workspaceName).then(data => {
        if (!data.success) {
            workspaceInfo.textContent = 'Fehler: ' + (data.error || 'Unbekannt');
            return;
        }
        currentWorkspaceData = data.workspace_data;
        workspaceInfo.textContent = 'Last Update: ' + (currentWorkspaceData.last_update || 'Unbekannt');

        checkAuthenticationStatus();
    });

    function checkAuthenticationStatus() {
        fetch('api/is_authenticated_workspace.php?name=' + encodeURIComponent(workspaceName))
            .then(r => r.json())
            .then(statusData => {
                if (!statusData.success) {
                    workspaceInfo.textContent = 'Fehler beim Prüfen der Authentifizierung: ' + (statusData.error||'');
                    return;
                }

                const isProtected = statusData.protected;
                const isAuth = statusData.authenticated;

                if (isProtected && !isAuth) {
                    authSection.style.display = 'block';
                    mainContent.style.display = 'none';
                } else {
                    authSection.style.display = 'none';
                    mainContent.style.display = 'block';
                    renderObjectives();
                    renderCheckins();
                }
            })
            .catch(err => {
                workspaceInfo.textContent = 'Fehler bei der Authentifizierungsprüfung: ' + err.message;
            });
    }

    if (authForm) {
        authForm.addEventListener('submit', e => {
            e.preventDefault();
            const pwd = document.getElementById('auth-password').value.trim();
            authenticateWorkspace(workspaceName, pwd).then(r => {
                if (r.success) {
                    alert('Authentifizierung erfolgreich.');
                    location.reload();
                } else {
                    alert('Fehler: ' + (r.error||'Falsches Passwort'));
                }
            });
        });
    }

    function renderObjectives() {
        const objectives = currentWorkspaceData.objectives || [];
        if (objectives.length === 0) {
            objectivesContainer.innerHTML = '<p>Keine Objectives vorhanden.</p>';
            return;
        }
        let html = '';
        objectives.forEach(obj => {
            html += `<div class="objective-item">
                <h4>${escapeHTML(obj.title)}</h4>
                <p>${escapeHTML(obj.description||'')}</p>
                <ul>
                ${(obj.key_results||[]).map(kr => {
                    return `<li>${escapeHTML(kr.description)} (Progress: ${(kr.progress*100).toFixed(1)}%) 
                    <button class="button btn-sm" onclick="editKR('${obj.id}','${kr.id}')">Bearbeiten</button>
                    <button class="button btn-sm btn-red" onclick="deleteKR('${obj.id}','${kr.id}')">Löschen</button></li>`;
                }).join('')}
                </ul>
                <button class="button btn-sm" onclick="addKR('${obj.id}')">KR hinzufügen</button>
                <button class="button btn-sm" onclick="editObjective('${obj.id}')">Bearbeiten</button>
                <button class="button btn-sm btn-red" onclick="deleteObjective('${obj.id}')">Löschen</button>
            </div>`;
        });
        objectivesContainer.innerHTML = html;
    }

    function renderCheckins() {
        const checkins = currentWorkspaceData.check_ins || [];
        if (checkins.length === 0) {
            checkinsContainer.innerHTML = '<p>Keine Check-Ins vorhanden.</p>';
            return;
        }
        let html = '<ul>';
        checkins.forEach(ci => {
            html += `<li>${escapeHTML(ci.date||'')} - ${escapeHTML(ci.notes||'')}</li>`;
        });
        html += '</ul>';
        checkinsContainer.innerHTML = html;
    }

    btnAddObjective.addEventListener('click', () => {
        showObjectiveModal();
    });

    btnAddCheckin.addEventListener('click', () => {
        showCheckinModal();
    });

    objectiveForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('obj_id').value;
        const title = document.getElementById('obj_title').value.trim();
        const desc = document.getElementById('obj_desc').value.trim();

        if (!title) {
            alert("Titel erforderlich");
            return;
        }

        const objectives = currentWorkspaceData.objectives || [];
        if (!id) {
            const newId = "o" + Date.now();
            objectives.push({ id: newId, title: title, description: desc, key_results: [] });
        } else {
            const obj = objectives.find(o=>o.id===id);
            if (obj) {
                obj.title = title;
                obj.description = desc;
            }
        }
        currentWorkspaceData.objectives = objectives;
        await saveAndReload();
    });

    krForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const objId = document.getElementById('kr_obj_id').value;
        const krId = document.getElementById('kr_id').value;
        const desc = document.getElementById('kr_desc').value.trim();
        const baseVal = parseFloat(document.getElementById('kr_base').value)||0;
        const targetVal = parseFloat(document.getElementById('kr_target').value)||0;
        const currVal = parseFloat(document.getElementById('kr_current').value)||0;

        if (!desc) {
            alert("Beschreibung erforderlich");
            return;
        }

        const obj = (currentWorkspaceData.objectives||[]).find(o=>o.id===objId);
        if (!obj) {
            alert("Objective nicht gefunden");
            return;
        }

        if (!krId) {
            const newKrId = "kr" + Date.now();
            if (!obj.key_results) obj.key_results=[];
            obj.key_results.push({
                id: newKrId,
                description: desc,
                base_value: baseVal,
                target_value: targetVal,
                current_value: currVal,
                progress: calcProgress(baseVal, targetVal, currVal)
            });
        } else {
            const kr = (obj.key_results||[]).find(k=>k.id===krId);
            if (kr) {
                kr.description = desc;
                kr.base_value = baseVal;
                kr.target_value = targetVal;
                kr.current_value = currVal;
                kr.progress = calcProgress(baseVal, targetVal, currVal);
            }
        }
        await saveAndReload();
    });

    checkinForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const notes = document.getElementById('checkin_notes').value.trim();
        const newCheckin = { date: new Date().toISOString().slice(0,10), notes: notes };
        if (!currentWorkspaceData.check_ins) currentWorkspaceData.check_ins=[];
        currentWorkspaceData.check_ins.push(newCheckin);
        await saveAndReload();
    });

    async function saveAndReload() {
        const res = await saveWorkspaceData(workspaceName, currentWorkspaceData);
        if (res.success) {
            location.reload();
        } else {
            alert("Speichern fehlgeschlagen: " + (res.error||''));
        }
    }

    window.editObjective = function(id) {
        const obj = (currentWorkspaceData.objectives||[]).find(o=>o.id===id);
        if (!obj) return;
        showObjectiveModal(obj);
    }

    window.deleteObjective = async function(id) {
        if (!confirm("Objective wirklich löschen?")) return;
        currentWorkspaceData.objectives = (currentWorkspaceData.objectives||[]).filter(o=>o.id!==id);
        await saveAndReload();
    }

    window.addKR = function(objId) {
        showKRModal(objId);
    }

    window.editKR = function(objId, krId) {
        const obj = (currentWorkspaceData.objectives||[]).find(o=>o.id===objId);
        if (!obj) return;
        const kr = (obj.key_results||[]).find(k=>k.id===krId);
        if (!kr) return;
        showKRModal(objId, kr);
    }

    window.deleteKR = async function(objId, krId) {
        if (!confirm("Key Result wirklich löschen?")) return;
        const obj = (currentWorkspaceData.objectives||[]).find(o=>o.id===objId);
        if (!obj) return;
        obj.key_results = (obj.key_results||[]).filter(k=>k.id!==krId);
        await saveAndReload();
    }

    function calcProgress(base, target, current) {
        if (target===base) return 0;
        return Math.max(0, Math.min((current - base)/(target - base), 1));
    }

    function showObjectiveModal(obj) {
        document.getElementById('obj_id').value = obj?obj.id:'';
        document.getElementById('obj_title').value = obj?obj.title:'';
        document.getElementById('obj_desc').value = obj?obj.description:'';
        openModal('#modal-objective');
    }

    function showKRModal(objId, kr) {
        document.getElementById('kr_obj_id').value = objId;
        document.getElementById('kr_id').value = kr?kr.id:'';
        document.getElementById('kr_desc').value = kr?kr.description:'';
        document.getElementById('kr_base').value = kr?kr.base_value:'';
        document.getElementById('kr_target').value = kr?kr.target_value:'';
        document.getElementById('kr_current').value = kr?kr.current_value:'';
        openModal('#modal-kr');
    }

    function showCheckinModal() {
        document.getElementById('checkin_notes').value = '';
        openModal('#modal-checkin');
    }

    function openModal(selector) {
        document.querySelector(selector).style.display='block';
    }

    document.querySelectorAll('.btn-cancel[data-close-modal]').forEach(btn=>{
        btn.addEventListener('click',(e)=>{
            document.querySelector(btn.dataset.closeModal).style.display='none';
        });
    });

    function escapeHTML(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
});
</script>
</body>
</html>
