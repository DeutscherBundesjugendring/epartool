---
layout: default
title: Emails
---


# Emails

The tool email composition subsystem is based on three basic concepts.

## Placeholder

Placeholder is a part of text of an email that will be replaced by some other text before sending. This can be useful when the email includes data that are specific to the given user and occasion. Consultation name, confirmation URL, users name are all examples of data to be inserted by a placeholder.
Some placeholders are available to use in all templates and in components while others might only be available in one or more templates. For example the `{{'{{from_name'}}}}` or `{{'{{send_date'}}}}` placeholders are always available in all templates and even in the ad hoc email editor. On the other hand `{{'{{confirmation_url'}}}}` placeholder is only available in templates where it makes sense, typically templates prompting the recipient to confirm something by visiting the supplied URL.

## Component

Components are predefined pieces of text that can be used in both templates and in the ad hoc email editor. Unlike placeholders, components can be created and deleted by admin. This is useful when some text is to be used in multiple templates and emails, especially if the text is subject to change. A mailing address or a unified email header/footer would be a good use for this feature.
The globally available placeholders can be used in components.

## Template

Template is a predefined text to be used in an email. Templates come in two types: system and admin. The system templates can not be removed as that would break the application, they can only be edited. Admin templates can be created by admin to be used in the ad hoc email form and can be deleted when they are not needed anymore.
Components and placeholders can be used within a template to insert some predefined text.

