# Release Notes for Elastic Export Billiger.de

## v1.1.4 (2018-04-30)

### Changed
- Laravel 5.5 update.

## v1.1.3 (2018-03-28)

### Changed
- The class FiltrationService is responsible for the filtration of all variations.
- Preview images updated.

## v1.1.2 (2018-03-19)

### Added
- Information was added to the tables in the User Guide.
- Info tab was added.

## v1.1.1 (2018-02-16)

### Changed
- Updated plugin short description.

## v1.1.0 (2017-12-28)

### Added
- The plugin takes the new fields "Stockbuffer", "Stock for variations without stock limitation" and "Stock for variations with not stock administration" into account.

## v1.0.9 (2017-11-30)

### Changed
- The export of items without a price will not be cancelled through internal logic anymore.
## v1.0.8 (2017-11-07)

### Fixed
- An issue was fixed which caused the shipping costs to be calculated depending on a predefined method of payment.

## v1.0.7 (2017-10-30)

### Changed
- Enhanced the plugin performance.

## v1.0.6 (2017-10-26)

### Added
- The column "delivery_sop" was added. This specifies if the item is available on SOP (Solute Order Platform).
- The column "stock_quantity" was added. This specifies the item stock if the item is available on SOP (Solute Order Platform).

## v1.0.5 (2017-10-20)

### Changed
- The export gets the result fields now from the ResultFieldsDataProvider within the ElasticExport plugin.

## v1.0.4 (2017-09-25)

### Changed
- The user guide was updated.

## v1.0.3 (2017-08-30)

### Added
- The column "old_price" was added. This allows the export of strikethrough prices.
- The column "images" was added. This allows the export of additional images.

### Fixed
- Shipping costs of 0.00 euro were not exported.

## v1.0.2 (2017-08-01)

### Fixed
- An issue was fixed which caused the stock filter to not work as intended.

## v1.0.1 (2017-07-18)

### Added
- The fields of the CSV file were extended to support new properties for billiger.de.

### Changed
- The plugin Elastic Export is now required to use the plugin format BilligerDE.

## v1.0.0 (2017-05-18)
 
### Added
- Added initial plugin files
