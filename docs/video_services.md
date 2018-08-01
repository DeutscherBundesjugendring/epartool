---
layout: default
title: Contributions
---


# Video Services

## Global settings

The ePartool app uses external services to handle video hosting. Presently videos can be hosted on:

* Facebook
* Youtube
* Vimeo

To globally enable a video hosting service:

For Facebook and Vimeo the correct credentials must be added to the `application/configs/config.local.ini` file. Youtube does not need any credentials. The Facebook credentials are the same that are used for Facebook [Social Login]({% link users.md %}#social-logins).

The video hosting service must be enabled in administration on page `/admin/settings/services`


## Discussion settings

To allow videos in discussions:

1. Enable videos globally
2. Ensure discussion is enabled for the given consultation on the main consultation settings page (`/admin/consultation/edit/kid/<consultation_id>`)
3. On the same page make sure the checkbox `Allow videos in Discussion` is checked.


## Question settings

To allow users to use videos as part of their consultation for a given question:

1. Enable videos globally
2. On the question detail page (`/admin/question/edit/kid/<consultation_id>/qid/<question_id>`) make sure that the checkbox `Allow video in contributions` is checked.
