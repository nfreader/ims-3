# IMS

An Incident Management System for tracking real-world events. 

## Structure 

**Incidents** are created under **agencies**. An **incident** can have individual **events**. These **events** can be updated with **comments** made by **users**. **Users** can be assigned **roles**, which are created under **agencies**. **Events** and **comments** use the **user**'s currently selected **role** to show author information. **Roles** are used to determine what level of access a **user** has for a given **incident**. 

## Permission Flags

* `VIEW_INCIDENT` - Ability to see an incident and all content within it, including events, comments, attachments, and the activity log.

* `EDIT_INCIDENT` - Grants the abilities to open/close an incident, or change other factors 

* `POST_UPDATES` - The ability to post events and comments on events

* `EDIT_UPDATES` - Controls access to editing events/comments. Also determines whether or not the user can append/prepend/replace an event with a comment.

* `ACTIVITY_LOG` - Lets the user post updates to the incident activity log 

* `UPDATE_ROLES` - If granted, allows the user to manage roles and permissions for a given incident

## TODO

* Email notifications
* Implement default comment actions for events (prepend, append, replace)
* Move uploads to S3
    * Make this optional