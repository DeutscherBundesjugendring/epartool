---
layout: default
title: Projects
---


# Projects

## Managing Projects

### Adding project to installation

#### GUI
The only way to create a project by the GUI is by using the installation wizard during setup. It will create a new database containing one project.

#### With server access
To add a project to an existing installation:

1. Open the script `data/create-project-<project_language>.sql` and edit the first line to use the desired project code. It must consist of two alphanumeric characters and it must be unique in the context of the database.
2. Run the modified SQL script against the database.


### Removing a project

Presently there is no supported way to remove a project.


### Setting the installation to use a given project

The project code must be set in the `application/configs/config.local.ini` in the directive `project`.
