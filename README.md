IntegerNet_EuropeanTax
=====================
Tax calculation independant of customer groups, using different tax IDs for different customer addresses

Facts
-----
- version: 0.4.0
- extension key: IntegerNet_EuropeanTax
- [extension on GitHub](https://github.com/integer-net/EuropeanTax)
- [direct download link](https://github.com/integer-net/EuropeanTax/archive/master.zip)

Description
-----------
This module creates an additional field in the customer group form called "Tax Class with valid VAT ID". It 
determines on the fly which tax class to use depending on a valid VAT ID being entered for the currently selected 
shipping address.

![Configuration Menu](https://www.integer-net.com/download/integernet-europeantax.png)

Read more about the context at [https://www.integer-net.com/tax-configuration-eu-for-b2b-and-b2c-stores/](https://www.integer-net.com/tax-configuration-eu-for-b2b-and-b2c-stores/).

Requirements
------------
- PHP >= 5.3.0

Compatibility
-------------
- Magento >= 1.6

Installation Instructions
-------------------------
1. Clone the module into your document root.
2. Update the fields "Tax Class with valid VAT ID" in the customer groups

Uninstallation
--------------
1. Remove all extension files from your Magento installation

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/integer-net/EuropeanTax/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Andreas von Studnitz, integer_net GmbH
[http://www.integer-net.com](http://www.integer-net.com)
[@integer_net](https://twitter.com/integer_net)

Licence
-------
[GNU General Public License 3.0](http://www.gnu.org/licenses/)

Copyright
---------
(c) 2016 integer_net GmbH
