---
layout: default
title: Terminology
---

# Terminology

## article
**context:** consultation, project<br />
**DB table:** articles

* Static page that can optionally be attached to a project or a consultation
* Location on the site is determined by its `ref_name` value
* If used in the context of a `consultation`, articles can be hierarchically organized into a tree one level deep


## consultation
**context:** project<br />
**DB table:** cnslt

* Top level entity for one consultation round
* Isolated in the sense that consultations do not depend on each other. The only exception is that `contributions` can be linked across consultations in the *Reactions & Impact* phase. 
* Can have one or more questions that serve as a starting point of a discussion
* Must belong to one or more projects


## contribution
**context:** consultation<br />
**DB table:** inpt

* `Users` response to a particular `question`
* Two types:
    * **Admin created contributions** are created by admin using the merge, split and copy operations in the voting preparation screen
    * **User created contributions** are created by one of the following methods:
        * Normal user submitting a contribution during the appropriate phase on the frontend.
        * Admin creates contribution with the button `New contribution` in the admin contribution list screen. Contribution created in this way always has to be associated with a non admin `user`


## discussion_post
**context:** contribution<br />
**DB table:** inpt_discussion

* A post in a linear discussion related to a given `contribution`
* Can contain text and embedded videos


## question
**context:** consultation<br />
**DB table:** quests

* Can be a question (in grammatical sense) or some statement that `users` can take a stance on.
* Serves as the starting point of discussion within a `consultation`
* `Users` reply to it by the means of `contributions`


## phase
**context:** consultation<br />
**DB table:** cnslt

* Each consultation has several phases. Not all are mandatory:
    * **Info:** During this phase the background information is distributed. This phase is active as soon as the `consultation` is created.
    * **Questions:** During this phase the `questions` are public for the users to take their stance on them. This phase is active as soon as the `consultation` is created.
    * **Contributions:** During this phase the users can submit `contributions` in response to the `questions`. This phase has two sub phases: 
        * `users` can show support
        * `users` can discuss the submitted `contributions`
    * **Voting:** During this phase `users` can vote on the `contributions`.
    * **Reactions & Impact:** During this phase admins post outside reactions to the `consultation` results. The reactions can then be linked among each other and with `contributions`.


## project
**context:** global<br />
**DB table:** proj

* Groups `consultations`
* Each is a separate website with its own administration and URL
* Projects sharing the same DB should always use the same language as they share some entities (i.e. `tags`).
* See: [Managing Projects]({{ site.baseurl }}{% link projects.md %}#managing-projects)


## reaction_file
**context:** consultation<br />
**DB table:** fowup_fls

* Represent an outside reaction to the outcome of the `consultation`.
* Entered and shown during the *Reactions & Impact* phase
* Can either be a real document in the form of `*.pdf`, `*.odt`, `*.doc` etc. or be a audio/video file


## reaction_snippet
**context:** reaction_file<br />
**DB table:** fowups

* An excerpt from a `reaction_file` attached to a particular `contribution`


## user
**context:** global<br />
**DB table:** users, user_consultation_info

* Anyone who uses the site (including admins)
* Created
    * manually by admins
    * automatically when a `contribution` is created
* Has two profiles:
    * global profile that is used to generate the default form values for when new `contributions` are submitted. Saved in the *users* db table.
    * consultation specific profile saved in the *user_consultation_info* db table
* Can represent:
    * individuals
    * groups with consultation specific `voting_weight`. The voting_right is granted to that user, and (s)he can pass on the casting of votes to other users by giving them the voting code.


## tag
**context:** global<br />
**DB table:** tgs

* Admins use it to organize `contributions`
* **Are shared among projects !!!**


## voting_right
**context:** consultation, user<br />
**DB table:** vt_right

* Assigns each `user` one string identifier (*voting_code*), which she can distribute to as many people as the `user` likes within her group: Each participant of the group uses the same voting_code, but uses different e-mail address to authenticate. There can be an unlimited number of people voting with one *voting_code* as the votes weight is adjusted according to the group_size.
* The *group_size* used for calculating the weight of the individual vote cast under one *voting_code* is determined by an admin and saved in this entity. Normally, the *group_size* value submitted by the user and saved in `user_consultation_info` would be used, but it can be overloaded here if needed.

**Example:** If you had 100 people voting in a group of size 10, then each vote would count 0.10 in order to reach the overall voting weight of 10 for your group.


## voting_weight
**context:** consultation<br />
**DB table:** -

* The value of each vote cast by the given user
* Rule of thumb is that not one or few (group) users should be allowed to have more weight then all other contributors. The mathematical formula is still not set clearly enough – that’s why we do it manually and not automatically.
* If the user has voting_weight = 0 the she is considered test voter. She can cast votes, but they are not counted towards the result


## voting_sub_user
**context:** consultation<br />
**DB table:** vt_grps

* A connecting entity between the `user` acting as a group leader and a visitor acting as a group member. The group leader must be a real `user`, but the group member is just an email address and might not have an associated record in db. The group leader and group member might or might not be the same user. The votes cast by the given group member are counted towards the voting results only if the voting_sub_user is confirmed.


## vote
**context:** user, contribution
**DB table:** vt_indiv

* A vote is a single vote given by a `voting_sub_user` to an individual `contribution`.


## superbutton
**context:** consultation<br />
**DB table:** -

* Can be enabled per `consultation` by admin.
* Has three parameters:
    * **Rating factor:** This number multiplied by the max point value in the given set of voting buttons defines for how many points the super_button is counted towards voting. Must be and integer greater than 1
    * **Number of clicks allowed:** Each user can only use the super_button this amount of times. This number must be greater then 0. When admin is setting this configuration, the total number of contributions for voting might not yet be known so there is no explicit upper limit. If the number is equal to or greater then the total number of contributions available for voting, then the user can use the super_button for each contribution.
    * **Superbutton label:** label shown to the user during voting


