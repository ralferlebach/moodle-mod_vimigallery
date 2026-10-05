moodle-mod_vimigallery
======================

[![Moodle Plugin CI](https://github.com/ralferlebach/moodle-mod_vimigallery/actions/workflows/moodle-ci.yml/badge.svg?branch=main)](https://github.com/ralferlebach/moodle-mod_vimigallery/actions?query=workflow%3A%22Moodle+Plugin+CI%22+branch%3Amain) [![ViMi Pad: Gallery](https://img.shields.io/badge/ViMi%20Pad-Gallery-0f6cbf)](https://ralferlebach.github.io/Moodle-ViMiPad-Plugins/)

The ViMi Gallery activity shows a set of ViMi Pad knowledge maps as an album - one map at a time, with previous and next controls - either embedded on the course page or behind a link.

ViMi Pad is not a single plugin but a family of four that work as one system. They are released
together, carry the same version number and the three satellites declare the activity as a
dependency, so a satellite is only ever as current as the activity it was qualified against.

* **mod_vimipad** is the editor and the data model: it owns the map itself - nodes, relations, containers, revisions, snapshots, annotations and grades - and exposes the public map API that the other three build on.
* **mod_vimigallery** is the reading view: it shows a set of maps as an album, one at a time, embedded on the course page or behind a link.
* **qtype_vimipad** turns a map into a quiz question: learners answer by drawing, and the answer is marked automatically against a reference map.
* **datafield_vimipad** adds a map as a field type in the Database activity, so a map can be one column of a collection.

This README documents **mod_vimigallery** - the second bullet point above. The other three plugins
are documented in their own repositories.

Because the responsibilities are split this way, the gallery never parses map data itself. It hands
every map it shows to the activity's map API, which is why a map that would not open in the editor
also never reaches a reader here.


Requirements
------------

This plugin requires Moodle 4.5+

It also requires the ViMi Pad activity, declared as a dependency in version.php and installed in the
same version (currently 1.0.0 / 2026100500):

* **mod_vimipad (ViMi Pad)** - required dependency: the gallery renders through its embeddable read-only editor and validates through its map API\
  https://github.com/ralferlebach/moodle-mod_vimipad
* **qtype_vimipad (ViMi Pad question type)** - optional, only needed to show reference maps from a Quiz\
  https://github.com/ralferlebach/moodle-qtype_vimipad
* **datafield_vimipad (ViMi Pad database field)** - optional, only needed to show maps from a Database activity\
  https://github.com/ralferlebach/moodle-datafield_vimipad

The two optional peers degrade gracefully: if they are absent, the corresponding source yields an
empty gallery rather than an error.


Motivation for this plugin
--------------------------

Maps are worth reading, not only worth making. Once a class has produced thirty concept maps, the
interesting work is comparing them - and that is exactly what the editor is not for: it is built for
one person changing one map.

This plugin separates reading from editing. It collects maps from wherever they already live -
submissions, database entries, quiz reference answers or uploaded files - and presents them as an
album a reader can page through, compare side by side and comment on, without any risk of changing
them.


Installation
------------

Install the plugin like any other plugin to folder
/mod/vimigallery

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it does not do anything to Moodle yet. Add a ViMi Gallery activity to a
course and choose where its maps come from: a ViMi Pad activity, a Database field, a Quiz question,
or files you upload.

The gallery has no site-wide settings. Everything is decided per activity: whether it appears
embedded on the course page or behind a link, whether author names are shown, whether readers may
comment, and whether the side-by-side comparison is offered. A gallery can read its source live or
keep a saved copy, which is what makes a snapshot of the class's work possible at a chosen moment.

If you want to learn more about using activity plugins in Moodle, please see https://docs.moodle.org/en/Activities.


Capabilities
------------

This plugin also introduces these additional capabilities:

* **mod/vimigallery:addinstance** - Add a ViMi Gallery to a course. By default, this is assigned to managers and editing teachers.
* **mod/vimigallery:view** - View a ViMi Gallery. By default, this is assigned to all participating roles.
* **mod/vimigallery:comment** - Comment on the maps in a gallery. By default, this is assigned to students and teachers.
* **mod/vimigallery:manageitems** - Curate the gallery: reorder maps and hide individual ones. By default, this is assigned to teachers and managers.


Scheduled Tasks
---------------

This plugin does not add any additional scheduled tasks.


How this plugin works / Pitfalls
--------------------------------

A gallery does not store maps of its own unless you ask it to. In live mode it reads its source each
time someone opens it, so it always shows the current state and obeys the access rules of the
activity the maps come from - a learner never sees through the gallery what they could not see
directly.

In static mode the gallery keeps a copy taken when you saved the activity, which is what makes it
usable as a record: the album stays as it was even if the underlying maps keep changing. That copy
can be refreshed from the source at any time.

Rendering goes through the activity's embeddable read-only editor, so a map looks here exactly as it
does in the editor, including its diagram form, and nothing on this page can modify it.

**Pitfall:** a gallery in static mode will not show later edits until you refresh it. That is the
point of the mode, but it surprises people who expect the album to follow the course.

**Pitfall:** author names are only as meaningful as the source allows. Maps materialised from group
work name the group's contributors, not a single author.

Theme support
-------------

This plugin is developed and tested on Moodle Core's Boost theme.
It should also work with Boost child themes, including Moodle Core's Classic theme. However, we can't support any other theme than Boost.


Plugin repositories
-------------------

This plugin is not published in the Moodle plugins repository.

The latest development version can be found on Github:
https://github.com/ralferlebach/moodle-mod_vimigallery

An overview of the whole plugin family is published at:
https://ralferlebach.github.io/Moodle-ViMiPad-Plugins/


Bug and problem reports / Support requests
------------------------------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/ralferlebach/moodle-mod_vimigallery/issues

We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.


Feature proposals
-----------------

Due to limited resources, the functionality of this plugin is primarily implemented for our own local needs and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/ralferlebach/moodle-mod_vimigallery/issues

Please create pull requests on Github:
https://github.com/ralferlebach/moodle-mod_vimigallery/pulls

We are always interested to read about your feature proposals or even get a pull request from you, but please accept that we can handle your issues only as feature _proposals_ and not as feature _requests_.


Moodle release support
----------------------

Due to limited resources, this plugin is only maintained for the most recent major release of Moodle as well as the most recent LTS release of Moodle. Bugfixes are backported to the LTS release. However, new features and improvements are not necessarily backported to the LTS release.

Apart from these maintained releases, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that this plugin still works with a new major release - please let us know on Github.

This plugin is designed to be compatible with all currently supported versions of Moodle, leveraging its latest APIs. However, if you are using a legacy version of Moodle, we kindly advise against installing or using this plugin. Instead, we strongly recommend updating your Moodle instance to a supported version to ensure security and compliance with current technological standards. Thank you for your understanding.


Translating this plugin
-----------------------

This Moodle plugin is provided with English and German language packs only. Translations into other languages must be managed through AMOS (https://lang.moodle.org), where they will become part of Moodle's official language pack.

As the plugin creator, we continue to maintain the German translation. For all other languages, we kindly ask you to contribute your translations directly in AMOS. These contributions will be reviewed by Moodle's official language pack maintainers before being included in the official repository.

Thank you for supporting the global Moodle community!


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send us a pull request on Github with modifications.


Maintainers
-----------

The plugin is maintained by\
Ralf Erlebach


Copyright
---------

The copyright of this plugin is held by\
Ralf Erlebach

Individual copyrights of individual developers are tracked in PHPDoc comments and Git commits.
