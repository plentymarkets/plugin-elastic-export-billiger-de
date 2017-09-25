
# ElasticExportBilligerDE plugin user guide
<div class="container-toc"></div>

## 1 Registering with billiger.de
billiger.de is a German price comparison portal certified by TÜV. The platform also offers test reports and customer reviews. Please note that this website is currently only available in German.

## 2 Setting up the data format BilligerDE-Plugin in plentymarkets
To use this format, you need the Elastic Export plugin.

Refer to the [Exporting data formats for price search engines](https://knowledge.plentymarkets.com/en/basics/data-exchange/exporting-data#30) page of the manual for further details about the individual format settings.

The following table lists details for settings, format settings and recommended item filters for the format **BilligerDE-Plugin**.
<table>
    <tr>
        <th>
            Settings
        </th>
        <th>
            Explanation
        </th>
    </tr>
    <tr>
        <th colspan="2">
            Settings
        </th>
    </tr>
    <tr>
        <td>
            Format
        </td>
        <td>
            Choose **BilligerDE-Plugin**.
        </td>        
    </tr>
    <tr>
        <td>
            Provisioning
        </td>
        <td>
            Choose <b>URL</b>.
        </td>        
    </tr>
    <tr>
        <td>
            File name
        </td>
        <td>
            The file name must have the ending <b>.csv</b> for billiger.de to be able to import the file successfully.
        </td>        
    </tr>
    <tr>
        <th colspan="2">
            Item filter
        </th>
    </tr>
    <tr>
        <td>
            Activ
        </td>
        <td>
            Choose **Active**.
        </td>        
    </tr>
    <tr>
        <td>
            Markets
        </td>
        <td>
            Choose **billiger.de**.
        </td>        
    </tr>
    <tr>
        <th colspan="2">
            Format settings
        </th>
    </tr>
    <tr>
		<td>
			Order referrer
		</td>
		<td>
			Choose one **referrer**.
		</td>
	</tr>
    <tr>
        <td>
            Image
        </td>
        <td>
            Choose **First image**.
        </td>        
    </tr>
    <tr>
		<td>
			Special price
		</td>
		<td>
			This option does not affect this format.
		</td>        
	</tr>
    <tr>
        <td>
            VAT note
        </td>
        <td>
            This option does not affect this format.
        </td>        
    </tr>
</table>

## 3 Overview of available columns
<table>
    <tr>
        <th>
            Column description
        </th>
        <th>
            Explanation
        </th>
    </tr>
    <tr>
        <td>
            aid
        </td>
        <td>
            <b>Required</b>
            The **SKU** based on the **variation ID**, if no was configured before.
            Die **SKU** auf Basis der **Varianten-ID**, falls für die Variante keine definiert wurde.
        </td>        
    </tr>
    <tr>
        <td>
            brand
        </td>
        <td>
            <b>Required</b>
            The <b>name of the manufacturer</b> of the item. The <b>external name</b> within <b>Settings » Items » Manufacturer</b> will be preferred if existing.
        </td>        
    </tr>
    <tr>
        <td>
            mpnr
        </td>
        <td>
            <b>Required</b><br>
            The <b>model</b> of the variations.
        </td>        
    </tr>
    <tr>
        <td>
            ean
        </td>
        <td>
            <b>Required</b><br>
            According to the format setting <b>Barcode</b>.
        </td>        
    </tr>
    <tr>
        <td>
            name
        </td>
        <td>
            <b>Required</b><br>
            According to the format setting <b>Item name</b>.
        </td>        
    </tr>
    <tr>
        <td>
            desc
        </td>
        <td>
            <b>Required</b><br>
            According to the format setting <b>Description</b>.
        </td>        
    </tr>
    <tr>
        <td>
            shop_cat
        </td>
        <td>
            <b>Required</b><br>
            <b>Category path of the standard category</b> for the <b>Client</b> configured in the format settings.
        </td>        
    </tr>
    <tr>
        <td>
            price
        </td>
        <td>
            <b>Required</b>
            The <b>retail price</b> of the variation.
        </td>        
    </tr>
    <tr>
        <td>
            ppu
        </td>
        <td>
            <b>Required</b>
            The <b>base price</b> of the variation.
        </td>        
    </tr>
    <tr>
        <td>
            link
        </td>
        <td>
        	<b>Required</b><br>
            The **product URL** of the variation.
        </td>        
    </tr>
    <tr>
        <td>
            image
        </td>
        <td>
            <b>Required</b><br>
            First image of the variation.
        </td>        
    </tr>
    <tr>
        <td>
            dlv_time
        </td>
        <td>
            <b>Required</b><br>
            According to the format setting <b>Item availabilty</b>.
        </td>        
    </tr>
    <tr>
        <td>
            dlv_cost
        </td>
        <td>
        	<b>Required</b><br>
            According to the format setting <b>shipping costs</b>.
        </td>        
    </tr>
    <tr>
        <td>
            pzn
        </td>
        <td>
        	<b>Required</b><br>
            The value of the property **pzn**.
        </td>        
    </tr>
    <tr>
        <td>
            promo_text
        </td>
        <td>
        	The value of the property **promo_text**.
        </td>        
    </tr>
    <tr>
        <td>
            voucher_text
        </td>
        <td>
        	The value of the property **voucher_text**.
        </td>        
    </tr>
    <tr>
        <td>
            eec
        </td>
        <td>
        	The value of the property **eec**.
        </td>        
    </tr>
    <tr>
        <td>
            light_socket
        </td>
        <td>
        	The value of the property **light_socket**.
        </td>        
    </tr>
    <tr>
        <td>
            wet_grip
        </td>
        <td>
        	The value of the property **wet_grip**.
        </td>        
    </tr>
    <tr>
        <td>
            fuel
        </td>
        <td>
        	The value of the property **fuel**.
        </td>        
    </tr>
    <tr>
        <td>
            rolling_noise
        </td>
        <td>
        	The value of the property **rolling_noise**.
        </td>        
    </tr>
    <tr>
        <td>
            hsn_tsn
        </td>
        <td>
        	The value of the property **hsn_tsn**.
        </td>        
    </tr>
    <tr>
        <td>
            dia
        </td>
        <td>
        	The value of the property **dia**.
        </td>        
    </tr>
    <tr>
        <td>
            sph_pwr
        </td>
        <td>
        	The value of the property **sph_pwr**.
        </td>        
    </tr>
    <tr>
        <td>
            cyl
        </td>
        <td>
        	The value of the property **cyl**.
        </td>        
    </tr>
    <tr>
        <td>
            axis
        </td>
        <td>
        	The value of the property **axis**.
        </td>        
    </tr>
    <tr>
        <td>
            size
        </td>
        <td>
        	The value of the property **size**.
        </td>        
    </tr>
    <tr>
        <td>
            color
        </td>
        <td>
        	The value of the property **color**.
        </td>        
    </tr>
    <tr>
        <td>
            gender
        </td>
        <td>
        	The value of the property **gender**.
        </td>        
    </tr>
    <tr>
        <td>
            material
        </td>
        <td>
        	The value of the property **material**.
        </td>        
    </tr>
    <tr>
        <td>
            class
        </td>
        <td>
        	The value of the property **class**.
        </td>        
    </tr>
    <tr>
		<td>
			features
		</td>
		<td>
			The value of the property **features**.
		</td>        
	</tr>
	<tr>
		<td>
			style
		</td>
		<td>
			The value of the property **style**.
		</td>        
	</tr>
	<tr>
    	<td>
    		old_price
    	</td>
    	<td>
    		The **price** of the variation in compared to the RRP. The higher price will be preferred.
    	</td>        
    </tr>
    <tr>
    	<td>
    		images
    	</td>
    	<td>
    		Further images of the variation (seperated by comma). 
    	</td>        
    </tr>        
</table>

## 4 License
This project is licensed under the GNU AFFERO GENERAL PUBLIC LICENSE.- find further information in the [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-billiger-de/blob/master/LICENSE.md).