# OKR WebApp

Diese OKR-WebApp bietet eine einfache Möglichkeit, Objectives und Key Results für verschiedene Teams und Benutzer zu verwalten – ganz ohne Datenbank. Daten werden in JSON-Dateien gespeichert. Die Anwendung ist schlank, läuft auf gängigen Shared-Hostings und erfordert lediglich PHP und Zugriff per FTP.

## Features

- **Workspaces:** Erstellen mehrerer Arbeitsbereiche (Workspaces), jeder mit eigenem Namensraum.
- **Passwortschutz:** Optionale Authentifizierung pro Workspace – sensible Daten lassen sich so einfach schützen.
- **OKR-Verwaltung:** Anlegen, Bearbeiten und Löschen von Objectives (Zielen) und Key Results.
- **Check-Ins:** Erfassung von regelmässigen Fortschritts-Updates (Check-Ins).
- **Suche & Paging:** Dynamische AJAX-Suche sowie Paginierung für eine grosse Anzahl von Workspaces.
- **Frontend:** Nutzung von HTML, CSS (Responsive Design) und Vanilla JavaScript (kein zusätzliches Framework erforderlich).
- **Backend:** Minimale PHP-Skripte für CRUD-Operationen auf JSON-Dateien.
- **Einfaches Deployment:** Nur Dateien per FTP hochladen. Keine Datenbank, keine zusätzliche Serverkonfiguration nötig.

## Voraussetzungen

- PHP-fähiges Webhosting (Shared-Hosting genügt).
- Schreibrechte auf das `data/`-Verzeichnis.
- Kein Composer oder Datenbankserver erforderlich.

## Installation

1. Repository clonen oder ZIP herunterladen.
2. Dateien per FTP auf den Webserver hochladen.
3. Sicherstellen, dass der Ordner `data/workspaces` Schreibrechte (CHMOD 0777 zum Testen) hat.
4. `index.php` im Browser aufrufen.

## Verwendung

- **Neuen Workspace erstellen:** Auf der Startseite (index.php) einen Namen und optional ein Passwort eingeben. Nach dem Erstellen wird automatisch zum Workspace weitergeleitet.
- **Workspaces anzeigen:** Über den Button "Arbeitsbereiche anzeigen" kann die Liste auf- bzw. zugeklappt werden.  
  Durchsuche und filtere die Workspaces nach Name, nutze Paging zur Navigation.
- **Objectives & Key Results:**  
  Im gewählten Workspace können Objectives und deren Key Results angelegt, bearbeitet oder gelöscht werden.  
  Check-Ins erlauben die Dokumentation des Fortschritts.
  
## Sicherheit

Passwörter werden mit `password_hash()` und `password_verify()` geprüft. Geschützte Workspaces erfordern eine Authentifizierung, bevor Änderungen möglich sind.

## Weiterentwicklung

- Erweiterbar um verschiedene Kadenzen (z. B. Unternehmens-, Team-, und Mitarbeiterebene).
- Anpassbare Berechtigungsmodelle oder Rollen.
- Design-Verbesserungen über das CSS-Stylesheet.

<img width="461" alt="image" src="https://github.com/user-attachments/assets/fb4fda74-0426-476c-8432-e6e5b361047b" />


## Support

Bei Fragen oder Problemen bitte ein Issue erstellen. Diese App ist als Beispiel konzipiert und kann nach eigenen Bedürfnissen angepasst werden.
