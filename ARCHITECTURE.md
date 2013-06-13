BROVINE ARCHITECTURE
====================
The Brovine gene database is built upon the CodeIgniter framework on the
backend, an MVC framework written in PHP. This runs on an Apache HTTP server
and stores data in MySQL. On the front end, the application is entirely in
Javascript, with a little help from several open source projects, listed later.

Backend
-------
[CodeIgniter](http://ellislab.com/codeigniter) is a powerful MVC framework that
Brovine uses. Almost all of the data transfer is done using AJAX, which gives
the user the effect of a seamless desktop application with few complete page
reloads.

Brovine&quot;s MySQL data store is a highly joined set of tables that represent
the complicated client data. Most queries require a join of four or five tables.
Details about the database implementation are explained on [the SQL schema help
page](/help/SQLSchema).

CodeIgniter&quot;s framework is split into several folders that hold each type
of file:

Genetic data, which is represented by a series of CSV files, is uploaded using
the [Uploadify](http://www.uploadify.com/) plugin. Uploadify is a Flash plugin that 
provides safe, seamless upload of data files to a server with minimal effort
required of the user. It also lets the user track the progress of the file
uploads, upload multiple files at one time, and cancel any uploads if necessary.
Brovine stores temporary files from Uploadify into the `/brovine/genedata-uploads`
folder while the system converts the files into data usable by Brovine.

The data itself is received from [TESS](/help/glossary#tess) as a set of Excel
files. Each gene that the researcher analyzes has its own set of 3 files which
must all be uploaded to Brovine if the gene is to be committed into the system:

  + **Job parameters**: contains information about the experiment conducted,
    and the gene; populates the experiment, comparison_type, and gene tables.
  + **Sequences**: contains information about the promoter sequence for the gene;
    populates the promoter_sequence table
  + **HITS-1**: contains information about the regulatory elements discovered in
    prior research; populates the regulatory_sequences, factor_matches, and
    study_pages tables.



Frontend
--------
The Brovine frontend is a . The following libraries are used to enhance the user
experience of Brovine:

  + [JQuery](http://jquery.com/): a powerful Javascript client library
  + [DataTables](http://www.datatables.net/): a JQuery plugin that provides
  extensive table support
  + [TokenInput](http://loopj.com/jquery-tokeninput/): a small but powerful
  JQuery plugin which enhances text boxes. The plugin searches through a set of
  pre-defined strings given a user input, which the user can then select.
  + [Bootstrap](http://twitter.github.io/bootstrap/index.html): a front-end
  HTML5 framework that makes web design simpler by offering basic styles
  for tables, lists, navigation, layout, and more.
