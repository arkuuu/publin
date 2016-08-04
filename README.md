Publin - Publication Index System
======
Publin allows you to upload and manage scholarly publications and expose them to the web. It is designed to be used by individual authors, groups or institutions. It aims at maximizing and speeding up the inclusion in major bibliographic databases in the area of computer sciences.


Features
======
* Submit publications, via web form or BibTeX import
* Edit publication and author metadata
* Display, add, modify and delete citations for a publication
* Browse and view publications, authors, journals etc
* Basic search function
* Full text file up- and download
* Export publications to various formats (e.g. BibTeX, RIS, ...)
* Import publications in SCF and BibTeX format
* Meta tag support for getting indexed by search engines (e.g. Dublin Core, HighwirePress Tags)
* Modular system for easy extension of export, import or meta tag formats
* OAI-PMH interface for being able to register as OAI Data Provider
* User authentication and authorization system with assignable roles and permissions
* Template system for easy customization of the user interface (using HTML&CSS)
* ...



Live Demo
======
A live demo of version v1.0.0 is available at [arkuuu.de/publin](http://arkuuu.de/publin/).

A live demo of the OAI-PMH interface is available at [arkuuu.de/publin/oai](http://arkuuu.de/publin/oai/?verb=Identify).


Requirements
======
A web server with:
* PHP >= 5.3.14
  * extension `mbstring`
  * extension `fileinfo`
* MySQL >= 5.5.38


Install
=====
* Change the `config/Config.php` file according to your needs
* Create a new folder called `publin` on your web server
* Copy all files into this folder
* Open the `install/` directory in your web browser and follow the instructions.
* Important: Remove the `install/` folder after the installation.



About the Project
======
This was a student project by Arne K. for his bachelor thesis at the University of Luebeck. View the task at [ifis.uni-luebeck.de](http://www.ifis.uni-luebeck.de/index.php?id=363) and the full thesis at [arkuuu.de/publin/?p=publication&id=67](http://arkuuu.de/publin/?p=publication&id=67)
