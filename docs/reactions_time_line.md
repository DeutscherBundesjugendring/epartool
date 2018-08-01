---
layout: default
title: Reaction Time Line
---


# Reaction Time Line 

The Reaction Time Line displays the relation between `contributions`, `reaction_snippets` and `reaction_documents`.


## Entities

There are three different types of entities. Each type shows different information in its box and can be related only to specific entities.

Relation between two `contributions` can be set up only during the voting preparation phase. All other relations are set up during the *Reactions & Impact* phase.

### contributions

`contributions` can be the origin of relation to other `contributions` and `reaction_snippets`.

In case this `contribution` was allowed to be voted upon it shows the rank it had in the voting.

### reaction_snippets

`reaction_snippets` can be the origin only of a relation leading to other `reaction_snippets`.

Users can like or dislike snippets by clicking link in the box. The number of likes/dislikes is shown.

Clicking the `reaction_snippet` opens a `reaction_document modal`.

A `reaction_snippet` can be text or multimedia embedding. It is not limited in length, therefore the main time line may only show long `reaction_snippets` in a shortened view.

### reaction_documents

`reaction_documents` can be the origin of relation leading to a `reaction_snippet`. They can not be part of any other relation.

Clicking the `reaction_document` opens a `reaction_document modal`.

`reaction_documents` can only be displayed if the starting element is a `reaction_snippet` and they are always positioned to the left of it.


## Columns

Starting entity must be a `contribution` or a `reaction_snippet`. `reaction_document` can never be a starting entity.

### Starting entity is a contribution

The starting `contribution` is always positioned in the left column and there is always exactly one. The second column is also populated on time line load and displays one or more `reaction_snippets` and/or `contributions`. The number in the arrow from the first to the second column is the same as the number of entities in the second column. All subsequent columns rightwards are empty on time line load.

In this situation all arrows point from left to right.

### Starting entity is a snippet

The starting `reaction_snippet` is always positioned in the center column and there is always exactly one. All other columns are empty on time line load.

In this situation the arrows can go in both directions. Arrows to the left of the starting `reaction_snippet` point right to left and arrows to the right of it always point left to right.


## Arrows

Columns are connected by arrows. Arrows can be left to right and right to left.

Each arrow shows a number that indicates the total number of relations originating in the entity to the left of the arrow if arrow is left to right or the total number of relations pointing to the entity to the right of the arrow if arrow is right to left. If the arrow was to display the number zero, it is not shown.

### Clicking an arrow pointing to non empty column

1. Displays all related entities in the column the arrow is pointing to. The number of the entities must be equal to the number in the clicked arrow.
2. Hides all entities in all subsequent columns.
3. If the time line box fits two or more columns, it scrolls itself to ensure that the column following the newly populated column is empty. A horizontal slider is shown as needed.

### Clicking the arrow pointing to empty column

1. Hides all entities in the column where the arrow originates except the one that owns the arrow.
2. Displays all entities in the column the arrow is pointing to. The number of the entities must be equal to the number in the arrow.
3. If the time line box fits two or more columns, it scrolls itself to ensure that the column following the newly populated column is empty. A horizontal slider is shown as needed.


## reaction_document modal

Contains the list of all `reaction_snippets` belonging to the displayed `reaction_document`. If the modal was opened by clicking a `reaction_snippet`, then the `reaction_snippet` which opened it shows link "Back to time line" which closes the overlay. All others show "Follow the path" link that reloads the time line and sets the given `reaction_snippet` as the starting entity.
