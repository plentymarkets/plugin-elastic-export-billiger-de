
# ElasticExportBilligerDE plugin user guide
<div class="container-toc"></div>

## 1 Registering with billiger.de

billiger.de is a German price comparison portal certified by TÜV. The platform also offers test reports and customer reviews. Please note that this website is currently only available in German.

## 2 Setting up the data format BilligerDE-Plugin in plentymarkets

By installing this plugin yo will receive the export format **BilligerDE-Plugin**. Use this format to exchange data between plentymarkets and billiger.de. It is required to install the Plugin Elastic export from the plentyMarketplace first before you can use the format **BilligerDE-Plugin** in plentymarkets.

Once both plugins are installed, you can create the export format **BilligerDE-Plugin**. Refer to the [Exporting data formats for price search engines](https://knowledge.plentymarkets.com/en/basics/data-exchange/exporting-data#30) page of the manual for further details about the individual format settings.

Creating a new export format:

1. Go to **Data » Elastic export**.
2. Click on **New export**.
3. Carry out the settings as desired. Pay attention to the information given in table 1.
4. **Save** the settings. 
→ The export format will be given an ID and it will appear in the overview within the **Exports** tab.

The following table lists details for settings, format settings and recommended item filters for the format **BilligerDE-Plugin**.

| **Setting**                                               | **Explanation** | 
| :---                                                      | :--- |
| **Settings**                                              |
| **Name**                                                  | Enter a name. The export format will be listed under this name in the overview within the **Exports** tab. |
| **Type**                                                  | Select the type **Item** from the drop-down menu. |
| **Format**                                                | Select **BilligerDE-Plugin**. |
| **Limit**                                                 | Enter a number. If you want to transfer more than 9,999 data records to the price search engine, then the output file will not be generated again for another 24 hours. This is to save resources. If more than 9,999 data records are necessary, the setting **Generate cache file** has to be active. |
| **Generate cache file**                                   | Place a check mark if you want to transfer more than 9,999 data records to the price search engine. The output file will not be generated again for another 24 hours. We recommend not to activate this setting for more than 20 export formats. This is to save resources. |
| **Provisioning**                                          | Select **URL**. This option generates a token for authentication in order to allow external access. |
| **Token, URL**                                            | If you have selected the option **URL** under **Provisioning**, then click on **Generate token**. The token is entered automatically. The URL is entered automatically if the token has been generated under **Token**. |
| **File name**                                             | The file name must have the ending **.csv** for billiger.de to be able to import the file successfully. |
| **Item filter**                                           |
| **Add item filters**                                      | Select an item filter from the drop-down menu and click on **Add**. There are no filters set in default. It is possible to add multiple item filters from the drop-down menu one after the other.<br/> **Variations** = Select **Transfer all** or **Only transfer main variations**.<br/> **Markets** = Select one market, several or **ALL**. The availability for all markets selected here has to be saved for the item. Otherwise, the export will not take place.<br/> **Currency** = Select a currency.<br/> **Category** = Activate to transfer the item with its category link. Only items belonging to this category will be exported.<br/> **Image** = Activate to transfer the item with its image. Only items with images will be transferred.<br/> **Client** = Select client.<br/> **Stock** = Select which stocks you want to export.<br/> **Flag 1 - 2** = Select the flag.<br/> **Manufacturer** = Select one, several or ALL manufacturers.<br/> **Active** = Only active variations will be exported. |
| **Format settings**                                       |
| **Product URL**                                           | Choose wich URL should be transferred to the price comparison portal, the item’s URL or the variation’s URL. Variation SKUs can only be transferred in combination with the Ceres store. |
| **Client**                                                | Select a client. This setting is used for the URL structure. |
| **URL parameter**                                         | Enter a suffix for the product URL if this is required for the export. If you have activated the **transfer** option for the product URL further up, then this character string is added to the product URL. |
| **Order referrer**                                        | Select the order referrer that should be assigned during the order import. |
| **Market account**                                        | Select the market account from the drop-down list. The selected referrer will be added to the product URL so that sales can be analysed later. |
| **Language**                                              | Select the language from the drop-down list. |
| **Item name**                                             | Select **Name 1**, **Name 2** or **Name 3**. These names are saved in the **Texts** tab of the item.<br/> Enter a number into the **Maximum number of characters (def. Text)** field if desired. This will specify how many characters should be exported for the item name. |
| **Preview text**                                          | Select the text that you want to transfer as preview text.<br/> Enter a number into the **Maximum number of characters (def. text)** field if desired. This will specify how many characters should be exported for the item name.<br/> Activate the option **Remove HTML tags** if you want HTML tags to be removed during the export. If you only want to allow specific HTML tags to be exported, then enter these tags into the field **Permitted HTML tags, separated by comma (def. Text)**. Use commas to separate multiple tags. |
| **Description**                                           | Select the text that you want to transfer as description.<br/> Enter a number into the **Maximum number of characters (def. text)** field if desired. This will specify how many characters should be exported for the description.<br/> Activate the option **Remove HTML tags** if you want HTML tags to be removed during the export. If you only want to allow specific HTML tags to be exported, then enter these tags into the field **Permitted HTML tags, separated by comma (def. Text)**. Use commas to separate multiple tags. |
| **Target country**                                        | Select the target country from the drop-down list. |
| **Barcode**                                               | Select the ASIN, ISBN or an EAN from the drop-down list. The barcode has to be linked to the order referrer selected above. If the barcode is not linked to the order referrer it will not be exported. |
| **Image**                                                 | Select **First image**. |
| **Image position of the energy efficiency label**         | Enter the position. Every image that should be transferred as an energy efficiency label must have this position. |
| **Stockbuffer**                                           | The stock buffer for variations with the limitation to the net stock. |
| **Stock for Variations without stock limitation**         | The stock for variations without stock limitation. |
| **The stock for variations with no stock administration** | The stock for variations without stock administration. |
| **Retail price**                                          | Select gross price or net price from the drop-down list. |
| **Offer price**                                           | This option does not affect this format. |
| **RRP**                                                   | Activate to transfer the RRP. |
| **Shipping costs**                                        | Activate this option if you want to use the shipping costs that are saved in a configuration. If this option is activated, then you will be able to select the configuration and the payment method from the drop-down lists.<br/> Activate the option **Transfer flat rate shipping charge** if you want to use a fixed shipping charge. If this option is activated, a value has to be entered in the line underneath. |
| **VAT note**                                              | This option does not affect this format. |
| **Item availability**                                     | Activate the **overwrite** option and enter item availabilities into the fields **1** to **10**. The fields represent the IDs of the availabilities. This will overwrite the item availabilities that are saved in the menu **System » Item » Availability**. |

## 3 Available columns for the export file

| **Column description** | **Explanation** |
| :---                   | :--- |
| aid                    | **Required**<br/> The **SKU** based on the **variation ID**, if no SKU was configured before. |
| brand                  | **Required**<br/> The **name of the manufacturer** of the item. The **external name** within **Settings » Items » Manufacturer** will be preferred if existing. |
| mpnr                   | **Required**<br/> The **model** of the variation. |
| ean                    | **Required**<br/> According to the format setting **Barcode**. |
| name                   | **Required**<br/> According to the format setting **Item name**. |
| desc                   | **Required**<br/> According to the format setting **Description**. |
| shop_cat               | **Required**<br/> **Category path of the standard category** for the **Client** configured in the format settings. |
| price                  | **Required**<br/> The **retail price** of the variation. |
| ppu                    | **Required**<br/> The **base price** of the variation. |
| link                   | **Required**<br/> The **product URL** of the variation. |
| images                 | **Required**<br/> First image of the variation. |
| dlv_time               | **Required**<br/> According to the format setting **Item availability**. |
| dlv_cost               | **Required**<br/> According to the format setting **Shipping costs**. |
| pzn                    | **Required**<br/> The value of the property **pzn**. |
| promo_text             | **Required**<br/> The value of the property **promo_text**. |
| voucher_text           | **Required**<br/> The value of the property **voucher_text**. |
| eec                    | **Required**<br/> The value of the property **eec**. |
| light_socket           | **Required**<br/> The value of the property **light_socket**. |
| wet_grip               | **Required**<br/> The value of the property **wet_grip**. |
| fuel                   | **Required**<br/> The value of the property **fuel**. |
| rolling_noise          | **Required**<br/> The value of the property **rolling_noise**. |
| hsn_tsn                | **Required**<br/> The value of the property **hsn_tsn**. |
| dia                    | **Required**<br/> The value of the property **dia**. |
| bc                     | **Required**<br/> The value of the property **bc**. |
| sph_pwr                | **Required**<br/> The value of the property **sph_pwr**. |
| cyl                    | **Required**<br/> The value of the property **cyl**. |
| axis                   | **Required**<br/> The value of the property **axis**. |
| size                   | **Required**<br/> The value of the property **size**. |
| color                  | **Required**<br/> The value of the property **color**. |
| gender                 | **Required**<br/> The value of the property **gender**. |
| material               | **Required**<br/> The value of the property **material**. |
| class                  | **Required**<br/> The value of the property **class**. |
| features               | **Required**<br/> The value of the property **features**. |
| style                  | **Required**<br/> The value of the property **style**. |
| old_price              | The **price** of the variation compared to the RRP. The higher price will be preferred. |
| images                 | Further images of the variation (seperated by comma). |
| delivery_sop           | The value of the property **delivery_sop**. The name of the property in the interface is 'Available for sale', under SOP (Solute Order Platform). |
| stock_quantity         | **Limitation**: from 0 to 9999<br/> **Content**: The **net stock of the variation**. If the variation is not limited to net stock, **999** is set as value. |

## 4 License
This project is licensed under the GNU AFFERO GENERAL PUBLIC LICENSE. Find further information in the [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-billiger-de/blob/master/LICENSE.md).
