---
layout: default
title: Users
---


# Users

## Social Logins

Users can log in by using external identity providers. For brevity I will refer to this login method as Social Login.
Presently the following identity providers are supported:

* Facebook
* Google

Social Logins are enabled by creating an account and application with the given identity provider and setting the obtained credentials in the `application/configs/config.local.ini` or by specifying them in the wizard during ePartool installation.

Social Login works similarly to the normal login.

Users must already have an account in the ePartool to be able to log in. The account can be obtained by submitting a contributions and filling the registration form or if an admin creates it manually.

To save the users the hassle of confirming their email, they can use the Social Login on the contribution submission form to load their email address. Since we trust the identity providers, we do not require confirmation of email address set this way. The rest of the form must be filled normally.
