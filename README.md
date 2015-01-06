Import Contacts Activity
========================

David Knoll, Future First
david@futurefirst.org.uk

An extension for CiviCRM. Automatically creates activities on bulk imported contacts.

Adds an activity type 'Created by Mass Upload'. An activity of this type
will be created for each bulk imported contact, with the source contact as
the user running the import, and the target contact as the contact being
imported.

Implementation details:
hook_civicrm_install to create the activity type, hook_civicrm_import to
create the activities. Exceptions are thrown on error.
