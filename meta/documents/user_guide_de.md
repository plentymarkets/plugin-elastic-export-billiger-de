
# User Guide für das ElasticExportBilligerDE Plugin
<div class="container-toc"></div>

## 1 Bei billiger.de registrieren

billiger.de ist ein deutsches Preisvergleichsportal mit TÜV-Zertifikat, das neben Preisvergleichen auch Testberichte und Nutzerbewertungen anbietet.

## 2 Das Format BilligerDE-Plugin in plentymarkets einrichten

Mit der Installation dieses Plugins erhalten Sie das Exportformat **BilligerDE-Plugin**, mit dem Sie Daten über den elastischen Export zu billiger.de übertragen. Um dieses Format für den elastischen Export nutzen zu können, installieren Sie zunächst das Plugin **Elastic Export** aus dem plentyMarketplace, wenn noch nicht geschehen.

Sobald beide Plugins im Ihrem System installiert sind, kann das Exportformat **BilligerDE-Plugin** erstellt werden. Mehr Informationen finden Sie auch auf der Handbuchseite [Daten exportieren](https://knowledge.plentymarkets.com/basics/datenaustausch/daten-exportieren#60).

Neues Exportformat erstellen:

1. Öffnen Sie das Menü **Daten » Elastischer Export**.
2. Klicken Sie auf **Neuer Export**.
3. Nehmen Sie die Einstellungen vor. Beachten Sie dazu die Erläuterungen in Tabelle 1.
4. **Speichern** Sie die Einstellungen.
→ Eine ID für das Exportformat **BilligerDE-Plugin** wird vergeben und das Exportformat erscheint in der Übersicht **Exporte**.

In der folgenden Tabelle finden Sie spezifische Hinweise zu den Einstellungen, Formateinstellungen und empfohlenen Artikelfiltern für das Format **BilligerDE-Plugin**.

| **Einstellung**                                     | **Erläuterung** | 
| :---                                                | :--- |
| **Einstellungen**                                   |
| **Name**                                            | Name eingeben. Unter diesem Namen erscheint das Exportformat in der Übersicht im Tab **Exporte**. |
| **Typ**                                             | Typ **Artikel** aus der Dropdown-Liste wählen. |
| **Format**                                          | **BilligerDE-Plugin** wählen. |
| **Limit**                                           | Zahl eingeben. Wenn mehr als 9999 Datensätze an die Preissuchmaschine übertragen werden sollen, wird die Ausgabedatei wird für 24 Stunden nicht noch einmal neu generiert, um Ressourcen zu sparen. Wenn mehr mehr als 9999 Datensätze benötigt werden, muss die Option **Cache-Datei generieren** aktiv sein. |
| **Cache-Datei generieren**                          | Häkchen setzen, wenn mehr als 9999 Datensätze an die Preissuchmaschine übertragen werden sollen. Um eine optimale Performance des elastischen Exports zu gewährleisten, darf diese Option bei maximal 20 Exportformaten aktiv sein. |
| **Bereitstellung**                                  | **URL** wählen. Mit dieser Option kann ein Token für die Authentifizierung generiert werden, damit ein externer Zugriff möglich ist. |
| **Token, URL**                                      | Wenn unter **Bereitstellung** die Option **URL** gewählt wurde, auf **Token generieren** klicken. Der Token wird dann automatisch eingetragen. Die URL wird automatisch eingetragen, wenn unter **Token** der Token generiert wurde. |
| **Dateiname**                                       | Der Dateiname muss auf **.csv** oder **.txt** enden, damit billiger.de die Datei erfolgreich importieren kann. |
| **Artikelfilter**                                   |
| **Artikelfilter hinzufügen**                        | Artikelfilter aus der Dropdown-Liste wählen und auf **Hinzufügen** klicken. Standardmäßig sind keine Filter voreingestellt. Es ist möglich, alle Artikelfilter aus der Dropdown-Liste nacheinander hinzuzufügen.<br/> **Varianten** = **Alle übertragen** oder **Nur Hauptvarianten übertragen** wählen.<br/> **Märkte** = Einen, mehrere oder **ALLE** Märkte wählen. Die Verfügbarkeit muss für alle hier gewählten Märkte am Artikel hinterlegt sein. Andernfalls findet kein Export statt.<br/> **Währung** = Währung wählen.<br/> **Kategorie** = Aktivieren, damit der Artikel mit Kategorieverknüpfung übertragen wird. Es werden nur Artikel, die dieser Kategorie zugehören, übertragen.<br/> **Bild** = Aktivieren, damit der Artikel mit Bild übertragen wird. Es werden nur Artikel mit Bildern übertragen.<br/> **Mandant** = Mandant wählen.<br/> **Bestand** = Wählen, welche Bestände exportiert werden sollen.<br/> **Markierung 1 - 2** = Markierung wählen.<br/> **Hersteller** = Einen, mehrere oder **ALLE** Hersteller wählen.<br/> **Aktiv** = Nur aktive Varianten werden übertragen. |
| **Formateinstellugen**                              |
| **Produkt-URL**                                     | Wählen, ob die URL des Artikels oder der Variante an das Preisportal übertragen wird. Varianten URLs können nur in Kombination mit dem Ceres Webshop übertragen werden. |
| **Mandant**                                         | Mandant wählen. Diese Einstellung wird für den URL-Aufbau verwendet. |
| **URL-Parameter**                                   | Suffix für die Produkt-URL eingeben, wenn dies für den Export erforderlich ist. Die Produkt-URL wird dann um die eingegebene Zeichenkette erweitert, wenn weiter oben die Option **übertragen** für die Produkt-URL aktiviert wurde. |
| **Auftragsherkunft**                                | Aus der Dropdown-Liste die Auftragsherkunft wählen, die beim Auftragsimport zugeordnet werden soll. |
| **Marktplatzkonto**                                 | Marktplatzkonto aus der Dropdown-Liste wählen. Die Produkt-URL wird um die gewählte Auftragsherkunft erweitert, damit die Verkäufe später analysiert werden können. |
| **Sprache**                                         | Sprache aus der Dropdown-Liste wählen. |
| **Artikelname**                                     | **Name 1**, **Name 2** oder **Name 3** wählen. Die Namen sind im Tab **Texte** eines Artikels gespeichert.<br/> Im Feld **Maximale Zeichenlänge (def. Text)** optional eine Zahl eingeben, wenn die Preissuchmaschine eine Begrenzung der Länge des Artikelnamen beim Export vorgibt. |
| **Vorschautext**                                    | Wählen, ob und welcher Text als Vorschautext übertragen werden soll.<br/> Im Feld **Maximale Zeichenlänge (def. Text)** optional eine Zahl eingeben, wenn die Preissuchmaschine eine Begrenzung der Länge des Vorschautextes beim Export vorgibt.<br/> Option **HTML-Tags entfernen** aktivieren, damit die HTML-Tags beim Export entfernt werden.<br/> Im Feld **Erlaubte HTML-Tags, kommagetrennt (def. Text)** optional die HTML-Tags eingeben, die beim Export erlaubt sind. Wenn mehrere Tags eingegeben werden, mit Komma trennen. |
| **Beschreibung**                                    | Wählen, welcher Text als Beschreibungstext übertragen werden soll.<br/> Im Feld **Maximale Zeichenlänge (def. Text)** optional eine Zahl eingeben, wenn die Preissuchmaschine eine Begrenzung der Länge der Beschreibung beim Export vorgibt.<br/> Option **HTML-Tags entfernen** aktivieren, damit die HTML-Tags beim Export entfernt werden.<br/> Im Feld **Erlaubte HTML-Tags, kommagetrennt (def. Text)** optional die HTML-Tags eingeben, die beim Export erlaubt sind. Wenn mehrere Tags eingegeben werden, mit Komma trennen. |
| **Zielland**                                        | Zielland aus der Dropdown-Liste wählen. |
| **Barcode**                                         | ASIN, ISBN oder eine EAN aus der Dropdown-Liste wählen. Der gewählte Barcode muss mit der oben gewählten Auftragsherkunft verknüpft sein. Andernfalls wird der Barcode nicht exportiert. |
| **Bild**                                            | **Erstes Bild** wählen. |
| **Bildposition des Energieetiketts**                | Position des Energieetikettes eintragen. Alle Bilder die als Energieetikette übertragen werden sollen, müssen diese Position haben. |
| **Bestandspuffer**                                  | Der Bestandspuffer für Varianten mit der Beschränkung auf den Netto Warenbestand. |
| **Bestand für Varianten ohne Bestandsbeschränkung** | Der Bestand für Varianten ohne Bestandsbeschränkung. |
| **Bestand für Varianten ohne Bestandsführung**      | Der Bestand für Varianten ohne Bestandsführung. |
| **Verkaufspreis**                                   | Brutto- oder Nettopreis aus der Dropdown-Liste wählen. |
| **Angebotspreis**                                   | Diese Option ist für dieses Format nicht relevant. |
| **UVP**                                             | Aktivieren, um den UVP zu übertragen. |
| **Versandkosten**                                   | Aktivieren, damit die Versandkosten aus der Konfiguration übernommen werden. Wenn die Option aktiviert ist, stehen in den beiden Dropdown-Listen Optionen für die Konfiguration und die Zahlungsart zur Verfügung. Option **Pauschale Versandkosten übertragen** aktivieren, damit die pauschalen Versandkosten übertragen werden. Wenn diese Option aktiviert ist, muss im Feld darunter ein Betrag eingegeben werden. |
| **MwSt.-Hinweis**                                   | Diese Option ist für dieses Format nicht relevant. |
| **Artikelverfügbarkeit**                            | Option **überschreiben** aktivieren und in die Felder **1** bis **10**, die die ID der Verfügbarkeit darstellen, Artikelverfügbarkeiten eintragen. Somit werden die Artikelverfügbarkeiten, die im Menü **System » Artikel » Verfügbarkeit** eingestellt wurden, überschrieben. |

## 3 Verfügbare Spalten der Exportdatei

| **Spaltenbezeichnung** | **Erläuterung** |
| :---                   | :--- |
| aid                    | **Pflichtfeld**<br/> Die **SKU** auf Basis der **Varianten-ID**, falls für die Variante keine definiert wurde. |
| brand                  | **Pflichtfeld**<br/> Der **Name des Herstellers** des Artikels. Der **Externe Name** unter **Einstellungen » Artikel » Hersteller** wird bevorzugt, wenn vorhanden. |
| mpnr                   | **Pflichtfeld**<br/> Das **Modell** der Variante. |
| ean                    | **Pflichtfeld**<br/> Entsprechend der Formateinstellung **Barcode**. |
| name                   | **Pflichtfeld**<br/> Entsprechend der Formateinstellung **Artikelname**. |
| desc                   | **Pflichtfeld**<br/> Entsprechend der Formateinstellung **Beschreibung**. |
| shop_cat               | **Pflichtfeld**<br/> **Kategoriepfad der Standard-Kategorie** für den in den Formateinstellungen definierten **Mandanten**. |
| price                  | **Pflichtfeld**<br/> Der **Verkaufspreis** der Variante. |
| ppu                    | **Pflichtfeld**<br/> Der **Grundpreis** der Variante. |
| link                   | **Pflichtfeld**<br/> Die **Produkt-URL** der Variante. |
| images                 | **Pflichtfeld**<br/> Erstes Bild der Variante. |
| dlv_time               | **Pflichtfeld**>br/> Gemäß der Formateinstellung **Artikelverfügbarkeit**. |
| dlv_cost               | **Pflichtfeld**<br/> Gemäß der Formateinstellung **Versandkosten**. |
| pzn                    | **Pflichtfeld**<br/> Der Wert des Merkmals **pzn**. |
| promo_text             | Der Wert des Merkmals **promo_text**. |
| voucher_text           | Der Wert des Merkmals **voucher_text**. |
| eec                    | Der Wert des Merkmals **eec**. |
| light_socket           | Der Wert des Merkmals **light_socket**. |
| wet_grip               | Der Wert des Merkmals **wet_grip**. |
| fuel                   | Der Wert des Merkmals **fuel**. |
| rolling_noise          | Der Wert des Merkmals **rolling_noise**. |
| hsn_tsn                | Der Wert des Merkmals **hsn_tsn**. |
| dia                    | Der Wert des Merkmals **dia**. |
| bc                     | Der Wert des Merkmals **bc**. |
| sph_pwr                | Der Wert des Merkmals **sph_pwr**. |
| cyl                    | Der Wert des Merkmals **cyl**. |
| axis                   | Der Wert des Merkmals **axis**. |
| size                   | Der Wert des Merkmals **size**. |
| color                  | Der Wert des Merkmals **color**. |
| gender                 | Der Wert des Merkmals **gender**. |
| material               | Der Wert des Merkmals **material**. |
| class                  | Der Wert des Merkmals **class**. |
| features               | Der Wert des Merkmals **features**. |
| style                  | Der Wert des Merkmals **style**. |
| old_price              | Der **Preis** einer Variante im Vergleich zum konfigurierten UVP. Der höhere Preis wird bevorzugt. |
| images                 | Weitere Bilder einer Variante (Kommagetrennt). |
| delivery_sop           | Der Wert des Merkmals **delivery_sop**. Der Name des Merkmals im plentymarkets Backend lautet "Zum Verkauf verfügbar", unter SOP (Solute Order Platform). |
| stock_quantity         | **Beschränkung**: 0 bis 9999<br/> **Inhalt**: Der **Netto-Warenbestand der Variante**. Bei Artikeln, die nicht auf den Netto-Warenbestand beschränkt sind, wird **999** übertragen. |


## 4 Lizenz

Das gesamte Projekt unterliegt der GNU AFFERO GENERAL PUBLIC LICENSE – weitere Informationen finden Sie in der [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-billiger-de/blob/master/LICENSE.md).
