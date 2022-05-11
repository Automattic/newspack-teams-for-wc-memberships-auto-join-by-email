# Newspack Teams for WooCommerce Memberships Auto-Join by Email

This Plugin is an extension for the [Teams for WooCommerce Memberships](https://woocommerce.com/products/teams-woocommerce-memberships/) plugin.

It automatically assigns newly registered Users to existing WooComm Team Memberships based on their email domain.

Instead of having to add WP Users to Team Memberships one by one, this plugin enables automatic adding of new WP Users to the corresponding Team Memberships based on their email’s @domain.com. If a new WP User has an email with a domain that matches the email domain of an Team Membership Owner, this new WP User will automatically get assigned to their Team, and therefore get access to that premium content

## Usage

The plugin adds a simple settings Section to the `WooCommerce` > `Settings` > `Memberships`, called "Teams Auto-Join by Email".

The settings enable defining "Excluded Email Domains". These are email domains are email domains which will get ignored/excluded when checking the new User's email domain. 

Please be extra mindful that by activating this Plugin, all the newly added Users will be automatically added to corresponding Teams, whose Team Owner's email domain matches their email domain. If you would like any of the existing Teams to be excluded from this behavior -- not to automatically grant their Membership to the newly joined Users -- you will need to add that particular Team Owner's email domain to the "Excluded Email Domains" list.  

For example, if there is a Team Membership Owner with the `@gmail.com` email domain, you will almost certainly want to add `gmail.com` to your Excluded Email Domains list, to prevent new Users with `@gmail.com` emails being added to a Team Membership which might have an Owner with a `@gmail.com` email too.

The Excluded Email Domains list supports the use of the `*` qualifier. For example, if you entered these two entries:

```
gmail.com,yahoo.*
```

the Plugin will then ignore all new Users with all the `@yahoo.*` possible emails too, e.g. `@yahoo.com`, `@yahoo.co.uk`, ...

### Security Recommendations

Since this Plugin automatically adds new Users to existing Team Memberships, it is recommended to enable email verification for all new Users before their registration is confirmed.
