// app.js
// Verantwortlich für die Interaktionen auf der Startseite (index.php):
// - Erstellen neuer Workspaces und direktes Weiterleiten zum neuen Workspace
// - Aufklappen einer Workspace-Liste mit AJAX-Suche und Paging

document.addEventListener('DOMContentLoaded', () => {
    const createForm = document.querySelector('#create-workspace-form');
    if (createForm) {
        createForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = createForm.querySelector('input[name="workspace_name"]').value.trim();
            const pwd = createForm.querySelector('input[name="workspace_password"]').value.trim();

            if (!name) {
                alert('Bitte einen Namen für den Workspace eingeben.');
                return;
            }

            createWorkspace(name, pwd).then(data => {
                if (data.success) {
                    // Weiterleitung zum neuen Workspace
                    const wsName = encodeURIComponent(data.workspace.name);
                    location.href = 'workspace.php?name=' + wsName;
                } else {
                    alert('Fehler: ' + data.error);
                }
            });
        });
    }

    // Elemente für die dynamische Workspace-Liste
    const toggleBtn = document.getElementById('toggle-workspaces');
    const workspaceControls = document.getElementById('workspace-controls');
    const workspaceList = document.getElementById('workspace-list');
    const workspaceUl = document.getElementById('workspace-ul');
    const workspaceSearch = document.getElementById('workspace-search');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const pageInfo = document.getElementById('page-info');

    let allWorkspaces = [];
    let filteredWorkspaces = [];
    let currentPage = 1;
    const pageSize = 10;

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            if (workspaceList.style.display === 'none') {
                // Liste aufklappen
                workspaceList.style.display = 'block';
                workspaceControls.style.display = 'block';
                if (allWorkspaces.length === 0) {
                    loadAndRenderWorkspaces();
                }
                toggleBtn.textContent = 'Arbeitsbereiche ausblenden';
            } else {
                // Liste zuklappen
                workspaceList.style.display = 'none';
                workspaceControls.style.display = 'none';
                toggleBtn.textContent = 'Arbeitsbereiche anzeigen';
            }
        });
    }

    if (workspaceSearch) {
        workspaceSearch.addEventListener('input', () => {
            currentPage = 1;
            applyFilters();
        });
    }

    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderWorkspacePage();
            }
        });
    }

    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredWorkspaces.length / pageSize);
            if (currentPage < totalPages) {
                currentPage++;
                renderWorkspacePage();
            }
        });
    }

    function loadAndRenderWorkspaces() {
        listWorkspaces().then(data => {
            if (data.success) {
                allWorkspaces = data.workspaces;
                applyFilters();
            } else {
                alert('Fehler beim Laden der Workspaces: ' + data.error);
            }
        });
    }

    function applyFilters() {
        const searchTerm = (workspaceSearch.value || '').trim().toLowerCase();
        filteredWorkspaces = allWorkspaces.filter(ws => ws.name.toLowerCase().includes(searchTerm));
        currentPage = 1;
        renderWorkspacePage();
    }

    function renderWorkspacePage() {
        workspaceUl.innerHTML = '';
        const total = filteredWorkspaces.length;
        const totalPages = Math.ceil(total / pageSize);
        if (totalPages === 0) {
            workspaceUl.innerHTML = '<li>Keine Workspaces gefunden.</li>';
            pageInfo.textContent = '';
            prevPageBtn.disabled = true;
            nextPageBtn.disabled = true;
            return;
        }

        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize, total);
        const pageItems = filteredWorkspaces.slice(startIndex, endIndex);

        pageItems.forEach(ws => {
            const li = document.createElement('li');
            li.className = 'workspace-item';
            const a = document.createElement('a');
            a.href = 'workspace.php?name=' + encodeURIComponent(ws.name);
            a.textContent = ws.name + (ws.protected ? ' (geschützt)' : '');
            li.appendChild(a);
            workspaceUl.appendChild(li);
        });

        pageInfo.textContent = `Seite ${currentPage} von ${totalPages}`;
        prevPageBtn.disabled = (currentPage === 1);
        nextPageBtn.disabled = (currentPage === totalPages);
    }
});
