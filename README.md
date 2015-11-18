# Admin Panel Lite

This repo is a sister of my [generic-admin-panel](https://github.com/davemn/generic-admin-panel) repo.
Where that repo exists as an exercise in the full Backbone SPA framework, **Admin Panel Lite** is an exercise in minimalism.

Specifically, this is an attempt to implement [CRUD](https://en.wikipedia.org/wiki/Create,_read,_update_and_delete) functionality with a small subset of Backbone, and vanilla PHP.
I used Backbone and [DataTables](https://www.datatables.net/) for message queueing and search/pagination, respectively.

## Creating an [SPA](https://en.wikipedia.org/wiki/Single-page_application) With a Dash of Backbone

Backbone realizes CRUD by providing structure to message passing, templating, and persistence.
Backbone is unique in the world of (popular) frameworks, in that it's not opinionated.
Here I've avoided using the templating and persistence layers, to test that assertion.

I've named events from the PoV of the storage layer, which also is the hub of activity driving the application:

**Storage Object**

* Listens to:
  * `user:add`
  * `user:remove`
  * `user:replace`
* Emits:
  * `storage:add`
  * `error:add`
  * `storage:remove`
  * `error:remove`
  * `storage:replace`
  * `error:replace`
  
## Document Schema

The documents stored in this application have the fields:

* Title
* Division
* File Type
* Business Area
* Category
* Subject
* File Attachment
  
## Persistence

All documents stored by this application are maintained in a (JSON) flat-file.
Document attachments are stored in a separate folder, renamed to the MD5 checksum of the file.
  
## User Interface

The UI is fairly minimal at this point.
I used Bootstrap tabs to separate the 4 pieces of (user-visible) functionality:

* A history of adds / deletes for the current session
* A form for adding a new document
* A DataTable for searching / selecting, and deleting a document
* A DataTable and form for searching / selecting, and updating individual fields in a document

