# Release Notes für Elastic Export Billiger.de

## v1.1.7 (2018-02-26)

### Hinzugefügt
- Die Spalte 'own_brand' wurde hinzugefügt.

## v1.1.6 (2019-01-21)

### Geändert
- Ein fehlerhafter Link im User Guide wurde korrigiert.

## v1.1.5 (2018-07-12)

### Geändert
- Ein fehlerhafter Link im User Guide wurde korrigiert.

## v1.1.4 (2018-04-30)

### Geändert
- Laravel 5.5 Update.

## v1.1.3 (2018-03-28)

### Geändert
- Die Klasse FiltrationService übernimmt die Filtrierung der Varianten.
- Vorschaubilder aktualisiert.

## v1.1.2 (2018-03-19)

### Hinzugefügt
- Die Tabellen im User Guide wurden ergänzt.
- Info-Tab hinzugefügt.

## v1.1.1 (2018-02-16)

### Geändert
- Plugin-Kurzbeschreibung wurde angepasst.

## v1.1.0 (2017-12-28)

### Hinzugefügt
- Das Plugin berücksichtigt die neuen Felder "Bestandspuffer", "Bestand für Varianten ohne Bestandsbeschränkung" und "Bestand für Varianten ohne Bestandsführung".

## v1.0.9 (2017-11-30)

### Geändert
- Der Export von Artikeln ohne Preis wird nicht mehr durch interne Logik verhindert.

## v1.0.8 (2017-11-07)

### Behoben
- Es wurde ein Fehler behoben, bei dem die Versandkosten je nach vordefinierter Zahlungsart berechnet wurden.

## v1.0.7 (2017-10-30)

### Geändert
- Plugin-Performance wurde optmimiert.

## v1.0.6 (2017-10-26)

### Hinzugefügt
- Die Spalte "delivery_sop" wurde hinzugefügt. Sie gibt an, ob ein Artikel auf SOP (Solute Order Platform) verfügbar ist.
- Die Spalte "stock_quantity" wurde hinzugefügt. Gibt den Artikelbestand an, wenn der Artikel auf SOP (Solute Order Platform) verfügbar ist.

## v1.0.5 (2017-10-20)

### Geändert
- Der Export bekommt nun die ResultFields von dem ResultFieldDataProvider in dem Elastic Export-Plugin.

## v1.0.4 (2017-09-25)

### Geändert
- Der User Guide wurde aktualisiert.

## v1.0.3 (2017-08-30)

### Hinzugefügt
- Die Spalte "old_price" wurde hinzugefügt. Dies erlaubt die Übertragung von Streichpreisen.
- Die Spalte "images" wurde hinzugefügt. Dies erlaubt die Übertragung zusätzlicher Bilder.

### Behoben
- Versandkosten von 0.00 Euro wurden nicht exportiert.

## v1.0.2 (2017-08-01)

### Behoben
- Ein Fehler wurde behoben der dazu geführt hat, dass der Bestandsfilter nicht wie vorgesehen funktionierte.

## v1.0.1 (2017-07-18)

### Hinzugefügt
- Die Felder der CSV-Datei wurden erweitert, um neue Merkmale für billiger.de zu unterstützen.

### Geändert
- Das Plugin Elastic Export ist nun Voraussetzung zur Nutzung des Plugin-Formats BilligerDE.

## v1.0.0 (2017-05-18)

### Hinzugefügt
- Initiale Plugin-Dateien hinzugefügt
