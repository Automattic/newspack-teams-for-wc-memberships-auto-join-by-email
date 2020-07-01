# Newspack Teams for WooCommerce Memberships Auto-Join by Email

This Plugin is an extension for the Teams for WooCommerce Memberships plugin.

It automatically assigns newly registered Users to existing WooComm Team Memberships based on their email domain.

If a new User's email domain matches the email domain of the Team Membership's Owner, the User will automatically get assigned to that Team.

## Usage

This plugin adds a simple settings Section to the `WooCommerce` > `Settings` > `Memberships`, called "Teams Auto-Join by Email".

These settings enable you to specify the "Excluded Email Domains". The Excluded Domains are email domains which get ignored/excluded from this logic. 

For example, if there is a Team Membership Owner with a `@gmail.com` domain, you will almost certainly want to add `gmail.com` to this list, so that newly registered users with `@gmail.com` email do not get added to this Membership.

The list is Coma Separated Value formatted, and enables use of the `*` qualifier.

For example, if you enter these two entries as Excluded Email Domains:

```
gmail.com,yahoo.*
```

the Plugin will ignore all newly registered users with all the `@yahoo.*` extensions too, e.g. `@yahoo.com`, `@yahoo.co.uk`, ...

### Security Recommendations

Since this Plugin automatically adds new Users to existing Team Memberships, it is recommended to enable email verification for new Users before registration is activated.