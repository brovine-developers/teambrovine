BROVINE ARCHITECTURE
====================
The Brovine gene database is built upon the CodeIgniter framework on the
server side, an MVC framework written in PHP. This runs on an Apache HTTP server
and stores data in MySQL. On the front end, the application is entirely in
Javascript, with a little help from several open source projects.

Server-Side
-----------
[CodeIgniter](http://ellislab.com/codeigniter) is a powerful MVC framework that
Brovine uses. Almost all of the data transfer is done using AJAX, which gives
the user the effect of a seamless desktop application with few complete page
reloads.

Brovine&quot;s MySQL data store is a highly joined set of tables that represent
the complicated client data. Most queries require a join of four or five tables.
Details about the database implementation are explained on [the SQL schema help
page](/help/SQLSchema).

Genetic data, which is represented by a series of CSV files, is uploaded using
the [Uploadify](http://www.uploadify.com/) plugin. Uploadify is a Flash plugin that 
provides safe, seamless upload of data files to a server with minimal effort
required of the user. It also lets the user track the progress of the file
uploads, upload multiple files at one time, and cancel any uploads if necessary.
Brovine stores temporary files from Uploadify into the `/brovine/genedata-uploads`
folder while the system converts the files into data usable by Brovine.

The data itself is received from [TESS](/help/glossary#tess) as a set of Excel
files. There is a set of example files [downloadable as a zip file]
(/files/experiment-data.zip). Each gene
that the researcher analyzes has its own set of 3 files which must all be
uploaded to Brovine if the gene is to be committed into the system:

  + **Job parameters**: contains information about the experiment conducted,
    and the gene; populates the experiment, comparison_type, and gene tables.
  + **Sequences**: contains information about the promoter sequence for the gene;
    populates the promoter_sequence table
  + **HITS-1**: contains information about the regulatory elements discovered in
    prior research; populates the regulatory_sequences, factor_matches, and
    study_pages tables.

Client-Side
-----------
The following libraries are used to enhance the user experience:

  + [JQuery](http://jquery.com/): a powerful Javascript client library
  + [DataTables](http://www.datatables.net/): a JQuery plugin that provides
  extensive table support
  + [TokenInput](http://loopj.com/jquery-tokeninput/): a small but powerful
  JQuery plugin which enhances text boxes. The plugin searches through a set of
  pre-defined strings given a user input, which the user can then select.
  + [Bootstrap](http://twitter.github.io/bootstrap/index.html): a front-end
  HTML5 framework that makes web design simpler by offering basic styles
  for tables, lists, navigation, layout, and more.

Each user view is essentially a set of tables that allow the user to drill down
to specific data points they want to see. For example, on the [Transcription
Factor Summary](/help/ViewDescriptions#tf_summary) page, the user is interested
in finding all genes in which a specific transcription factor occurs. So the
first table lets the user select a transcription factor by name, and the second
table shows all genes which have the selected factor.

Each table is populated on the back end by a method in the ajax controller,
which is located at `/brovine/application/controllers/ajax.php`. The DataTables
library handles searching (via the box above each table), sorting, and filtering
on the client side.

There are several other features of the Transcription Factor Summary page that
are worth mentioning. The "Filter by Regulation" box is an example of a
TokenInput text box. The purpose of this text box is to allow users to filter
their results by [regulation](/help/glossary#regulation). As the user starts 
typing, the TokenInput library sends whatever prefix they have typed to the
`getRegHints` method on the  ajax controller, which attempts to match their
prefix term with any of the regulation types in the database.

Javascript Architecture
-----------------------
Each view has its own javascript file - for example, the ExperimentHierarchy
javascript code is held inside `experimentHierarchy.js`. However, there is also
the `common.js` code which holds methods useful for all of the views. It
contains the `updateSelectList` and `updateMultiSelectList` methods, which
set up event handlers and handle the AJAX calls for each table.

Here is a list of views and their corresponding Javascript code:

 + **Experiment Hierarchy**: `experimentHierarchy.js`
 + **Transcription Factor Search**: `tfSearch.js`
 + **Transcription Factor Subtract**: [See the next section]()
 + **Gene Summary**: `geneSummary.js`
 + **Transcription Factor Summary**: `tfSummary.js`
 + **Gene Search**: `geneSearch.js`
 + **Transcription Factor Popularity**: `tfPop.js`
 + **Frequent Transcription Factors**: `experimentHierarchy.js`

The file `scripture.js` is responsible for the local download functionality
which is present on some Brovine views - the [Transcription Factor Subtract
page](help/ViewDescriptions#tf_summary), for example. Instead of generating a
file on the server and sending it back to the user, this Javascript code
generates a `data:octet-stream` link which lets the user extract the data without
making another AJAX call.

The file `upload.js` talks to the Uploadify flash software to notify the user
about the status of file uploads on [the Upload page](/Upload).

New Javascript Architecture
---------------------------
The creators of Brovine recognized that the current Javascript code is a huge
mess, but under the weight of deadlines found no reason to change the design.
However, a significant effort has been made to create a system that was more
reasonable. This effort is located in the `commonjs` folder in the Javascript
folder. Currently, the Transcription Factor Subtract page is the only view to
use the new Javascript architecture.


