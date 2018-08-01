---
layout: default
title: Contributions
---


# Contributions

## Contribution states

### Confirmation by admin

|is_confirmed=0               | blocked, hence not visible<br />
|is_confirmed=1               | not blocked, hence visible if `is_confirmed_by_user` is 1<br />
|is_confirmed=null            | not yet seen by admin, visible if `is_confirmed_by_user` is 1


### Confirmation by user

|is_confirmed_by_user=0       | means blocked, hence not visible<br />
|is_confirmed_by_user=1       | means approved, hence visible if `is_confirmed` is not 0<br />
|is_confirmed_by_user=null    | nor confirmed nor blocked, not visible


### Voting status

|is_votable=1                 | appears in voting, no matter what `is_confirmed` says.<br />
|is_votable=0                 | does not appear in voting, no matter what `is_confirmed` says<br />
|is_votable=null              | not checked by admin team yet, does not appear in voting no matter what `is_confirmed` says


## User created contribution

![user contribution create]({{ site.baseurl }}/images/user_contribution_create.png)
