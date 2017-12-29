
# User Guide für das ElasticExportBilligerDE Plugin
<div class="container-toc"></div>

## 1 Bei billiger.de registrieren
billiger.de ist ein deutsches Preisvergleichsportal mit TÜV-Zertifikat, das neben Preisvergleichen auch Testberichte und Nutzerbewertungen anbietet.

## 2 Das Format BilligerDE-Plugin in plentymarkets einrichten
Um dieses Format nutzen zu können, benötigen Sie das Plugin Elastic Export.

Auf der Handbuchseite [Daten exportieren](https://www.plentymarkets.eu/handbuch/datenaustausch/daten-exportieren/#4) werden allgemein die einzelnen Formateinstellungen beschrieben.

In der folgenden Tabelle finden Sie spezifische Hinweise zu den Einstellungen, Formateinstellungen und empfohlenen Artikelfiltern für das Format **BilligerDE-Plugin**.
<table>
    <tr>
        <th>
            Einstellung
        </th>
        <th>
            Erläuterung
        </th>
    </tr>
    <tr>
        <th colspan="2">
            Einstellungen
        </th>
    </tr>
    <tr>
        <td>
            Format
        </td>
        <td>
            Das Format <b>BilligerDE-Plugin</b> wählen.
        </td>        
    </tr>
    <tr>
        <td>
            Bereitstellung
        </td>
        <td>
            Die Bereitstellung <b>URL</b> wählen.
        </td>        
    </tr>
    <tr>
        <td>
            Dateiname
        </td>
        <td>
            Der Dateiname muss auf <b>.csv</b> enden, damit billiger.de die Datei erfolgreich importieren kann.
        </td>        
    </tr>
    <tr>
        <th colspan="2">
            Artikelfilter
        </th>
    </tr>
    <tr>
        <td>
            Aktiv
        </td>
        <td>
            <b>Aktiv</b> wählen.
        </td>        
    </tr>
    <tr>
        <td>
            Märkte
        </td>
        <td>
            <b>billiger.de</b> wählen.
        </td>        
    </tr>
    <tr>
        <th colspan="2">
            Formateinstellungen
        </th>
    </tr>
    <tr>
		<td>
			Auftragsherkunft
		</td>
		<td>
			Eine **Herkunft** wählen.
		</td>
	</tr>
    <tr>
        <td>
            Bild
        </td>
        <td>
            <b>Erstes Bild</b> wählen.
        </td>        
    </tr>
    <tr>
    	<td>
    		Bestandspuffer
    	</td>
    	<td>
    		Der Bestandspuffer für Varianten mit der Beschränkung auf den Netto Warenbestand.
    	</td>        
    </tr>
    <tr>
    	<td>
    		Bestand für Varianten ohne Bestandsbeschränkung
    	</td>
    	<td>
    		Der Bestand für Varianten ohne Bestandsbeschränkung.
    	</td>        
    </tr>
    <tr>
    	<td>
    		Bestand für Varianten ohne Bestandsführung
    	</td>
    	<td>
    		Der Bestand für Varianten ohne Bestandsführung.
    	</td>        
    </tr>
    <tr>
		<td>
			Angebotspreis
		</td>
		<td>
			Diese Option ist für dieses Format nicht relevant.
		</td>        
	</tr>
    <tr>
        <td>
            Versandkosten
        </td>
        <td>
            Es werden die Zahlungsarten gemäß der Formateinstellung <b>Versandkosten</b> übermittelt.
        </td>        
    </tr>
    <tr>
        <td>
            MwSt.-Hinweis
        </td>
        <td>
            Diese Option ist für dieses Format nicht relevant.
        </td>        
    </tr>
    <tr>
        <td>
            Artikelverfügbarkeit
        </td>
        <td>
            Der <b>Name der Artikelverfügbarkeit</b> unter <b>Einstellungen » Artikel » Artikelverfügbarkeit</b> oder die Übersetzung gemäß der Formateinstellung <b>Artikelverfügbarkeit überschreiben</b>.
        </td>        
    </tr>
</table>

## 3 Übersicht der verfügbaren Spalten
<table>
    <tr>
        <th>
            Spaltenbezeichnung
        </th>
        <th>
            Erläuterung
        </th>
    </tr>
    <tr>
        <td>
            aid
        </td>
        <td>
            <b>Pflichtfeld</b>
            Die **SKU** auf Basis der **Varianten-ID**, falls für die Variante keine definiert wurde.
        </td>        
    </tr>
    <tr>
        <td>
            brand
        </td>
        <td>
            <b>Pflichtfeld</b>
            Der <b>Name des Herstellers</b> des Artikels. Der <b>Externe Name</b> unter <b>Einstellungen » Artikel » Hersteller</b> wird bevorzugt, wenn vorhanden.
        </td>        
    </tr>
    <tr>
        <td>
            mpnr
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Das <b>Model</b> der Variante.
        </td>        
    </tr>
    <tr>
        <td>
            ean
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Entsprechend der Formateinstellung <b>Barcode</b>.
        </td>        
    </tr>
    <tr>
        <td>
            name
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Entsprechend der Formateinstellung <b>Artikelname</b>.
        </td>        
    </tr>
    <tr>
        <td>
            desc
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Entsprechend der Formateinstellung <b>Beschreibung</b>.
        </td>        
    </tr>
    <tr>
        <td>
            shop_cat
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Kategoriepfad der Standard-Kategorie</b> für den in den Formateinstellungen definierten <b>Mandanten</b>.
        </td>        
    </tr>
    <tr>
        <td>
            price
        </td>
        <td>
            <b>Pflichtfeld</b>
            Der <b>Verkaufspreis</b> der Variante.
        </td>        
    </tr>
    <tr>
        <td>
            ppu
        </td>
        <td>
            <b>Pflichtfeld</b>
            Der <b>Grundpreis</b> der Variante.
        </td>        
    </tr>
    <tr>
        <td>
            link
        </td>
        <td>
        	<b>Pflichtfeld</b><br>
            Die **Produkt-URL** der Variante.
        </td>        
    </tr>
    <tr>
        <td>
            image
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Erstes Bild der Variante.
        </td>        
    </tr>
    <tr>
        <td>
            dlv_time
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Gemäß der Formateinstellung <b>Artikelverfügbarkeit</b>.
        </td>        
    </tr>
    <tr>
        <td>
            dlv_cost
        </td>
        <td>
        	<b>Pflichtfeld</b><br>
            Gemäß der Formateinstellung <b>Versandkosten</b>.
        </td>        
    </tr>
    <tr>
        <td>
            pzn
        </td>
        <td>
        	<b>Pflichtfeld</b><br>
            Der Wert des Merkmals **pzn**.
        </td>        
    </tr>
    <tr>
        <td>
            promo_text
        </td>
        <td>
        	Der Wert des Merkmals **promo_text**.
        </td>        
    </tr>
    <tr>
        <td>
            voucher_text
        </td>
        <td>
        	Der Wert des Merkmals **voucher_text**.
        </td>        
    </tr>
    <tr>
        <td>
            eec
        </td>
        <td>
        	Der Wert des Merkmals **eec**.
        </td>        
    </tr>
    <tr>
        <td>
            light_socket
        </td>
        <td>
        	Der Wert des Merkmals **light_socket**.
        </td>        
    </tr>
    <tr>
        <td>
            wet_grip
        </td>
        <td>
        	Der Wert des Merkmals **wet_grip**.
        </td>        
    </tr>
    <tr>
        <td>
            fuel
        </td>
        <td>
        	Der Wert des Merkmals **fuel**.
        </td>        
    </tr>
    <tr>
        <td>
            rolling_noise
        </td>
        <td>
        	Der Wert des Merkmals **rolling_noise**.
        </td>        
    </tr>
    <tr>
        <td>
            hsn_tsn
        </td>
        <td>
        	Der Wert des Merkmals **hsn_tsn**.
        </td>        
    </tr>
    <tr>
        <td>
            dia
        </td>
        <td>
        	Der Wert des Merkmals **dia**.
        </td>        
    </tr>
    <tr>
        <td>
            sph_pwr
        </td>
        <td>
        	Der Wert des Merkmals **sph_pwr**.
        </td>        
    </tr>
    <tr>
        <td>
            cyl
        </td>
        <td>
        	Der Wert des Merkmals **cyl**.
        </td>        
    </tr>
    <tr>
        <td>
            axis
        </td>
        <td>
        	Der Wert des Merkmals **axis**.
        </td>        
    </tr>
    <tr>
        <td>
            size
        </td>
        <td>
        	Der Wert des Merkmals **size**.
        </td>        
    </tr>
    <tr>
        <td>
            color
        </td>
        <td>
        	Der Wert des Merkmals **color**.
        </td>        
    </tr>
    <tr>
        <td>
            gender
        </td>
        <td>
        	Der Wert des Merkmals **gender**.
        </td>        
    </tr>
    <tr>
        <td>
            material
        </td>
        <td>
        	Der Wert des Merkmals **material**.
        </td>        
    </tr>
    <tr>
        <td>
            class
        </td>
        <td>
        	Der Wert des Merkmals **class**.
        </td>        
    </tr>
    <tr>
		<td>
			features
		</td>
		<td>
			Der Wert des Merkmals **features**.
		</td>        
	</tr>
	<tr>
		<td>
			style
		</td>
		<td>
			Der Wert des Merkmals **style**.
		</td>        
	</tr>
	<tr>
    	<td>
    		old_price
    	</td>
    	<td>
    		Der **Preis** einer Variante im Vergleich zum konfigurierten UVP. Der höhere Preis wird bevorzugt.
    	</td>        
    </tr>
    <tr>
    	<td>
    		images
    	</td>
    	<td>
    		Weitere Bilder einer Variante (Komma getrennt). 
    	</td>        
    </tr>
    <tr>
        <td>
            delivery_sop
        </td>
        <td>
            Der Wert des Merkmals **delivery_sop**. Der Name des Merkmals im plentymarkets Backend lautet "Zum Verkauf verfügbar", unter SOP (Solute Order Platform).
        </td>        
    </tr>
    <tr>
        <td>
            stock_quatitiy
        </td>
        <td>
            <b>Beschränkung:</b> 0 bis 9999<br>
            <b>Inhalt:</b> Der <b>Netto-Warenbestand der Variante</b>. Bei Artikeln, die nicht auf den Netto-Warenbestand beschränkt sind, wird <b>999</b> übertragen.
        </td>        
    </tr> 
</table>

## 4 Lizenz
Das gesamte Projekt unterliegt der GNU AFFERO GENERAL PUBLIC LICENSE – weitere Informationen finden Sie in der [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-billiger-de/blob/master/LICENSE.md).
