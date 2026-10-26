
# Paystack CS Cart Plugin

Welcome to the Paystack CS Cart plugin repository on GitHub. 

Here you can browse the source code, look at open issues and keep track of development.

## Requirements

- PHP with the 21 Oct 2026 security fixes for critical remote code execution flaws (GHSA-w8hw-w9fx-j79w, GHSA-6wmf-4rv5-p3rp; [php.net advisory](https://www.php.net/releases/8_2_35.php)): **8.2.35, 8.3.36, 8.4.27, 8.5.12 or later**. PHP 8.1 and older no longer receive security fixes.
- CS-Cart / Multi-Vendor **4.18.1 or later**, the first version that supports PHP 8.2 ([CS-Cart system requirements](https://docs.cs-cart.com/latest/install/system_requirements.html)). CS-Cart 4.17.x and older cannot run PHP 8.2 or later, so they cannot run a patched PHP; please upgrade CS-Cart. As of CS-Cart's [September 2026 announcement](https://www.cs-cart.com/blog/php-and-postgresql-support-in-cs-cart/), PHP 8.2 and 8.3 are fully supported and support for 8.4 and 8.5 is still in development, so use PHP 8.2.35 or 8.3.36 (or later patch releases) for now.

## Installation
1. Ensure you have the latest version of CS Cart installed (at least 4.18.1, see Requirements above).
2. Download the zip of this repo.
3. Inside the file downloaded above is a file called 'install_paystack.sql'. This has to be executed against your cscart database. You can use phpmyadmin to import the file into your cscart database or copy paste the content and run directly into your mysql shell.
4. Upload rest of the contents of the plugin to your CS Cart Installation directory (content of app folder goes in app folder, content of design folder in design folder).

## Configuration

1. Log into CS-Cart as administrator (http://cscart_installation/admin.php). Navigate to Administration / Payment Methods.
2. Click the "+" to add a new payment method.
3. Choose Paystack from the list and then click save. For template, choose "cc_outside.tpl"
4. Click the 'Configure' tab.
5. Enter your Paystack Key ID and Key Secret which you can get from Paystack Dashboard.
6. Click 'Save'

## Documentation

* [Paystack Documentation](https://developers.paystack.co/v2.0/docs/)
* [Paystack Helpdesk](https://paystack.com/help)

## Support

For bug reports and feature requests directly related to this plugin, please use the [issue tracker](https://github.com/PaystackHQ/plugin-cs-cart/issues). 

For general support or questions about your Paystack account, you can reach out by sending a message from [our website](https://paystack.com/contact).

## Community

If you are a developer, please join our Developer Community on [Slack](https://slack.paystack.com).

## Contributing to the CS Cart plugin

If you have a patch or have stumbled upon an issue with the CS Cart plugin, you can contribute this back to the code. Please read our [contributor guidelines](https://github.com/PaystackHQ/plugin-cs-cart/blob/master/CONTRIBUTING.md) for more information how you can do this.
