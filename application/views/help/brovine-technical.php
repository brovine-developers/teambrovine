<h1>Brovine Technical Description</h1>

<h2>Overview</h2>
<a href="#overview"></a>
<p>The Brovine gene database is built upon the CodeIgniter framework on the backend, an MVC framework written in PHP. This runs on an Apache HTTP server, and it uses a MySQL database to store all of the genetic data. On the front end, the application is entirely in Javascript, using several open source projects, including JQuery, DataTables, and TokenInput.</p>
<p>Each page, which represents a “view” which the customers find useful, contains tables that display the genetic data to the user, as well as let the user drill down to more specific data. For example, on each page there are tables which show the species and experiments the customers have uploaded to the application. Customers can select one or more species, and Brovine will filter the experiment table to show only experiments with the selected species.</p>

<h1 id="brovinearchitecture">BROVINE ARCHITECTURE</h1> 
<p>The Brovine gene database is built upon the CodeIgniter framework on the 
server side, an MVC framework written in PHP. This runs on an Apache HTTP server 
and stores data in MySQL. On the front end, the application is entirely in 
Javascript, with a little help from several open source projects.</p> <h2 
  id="server-side">Server-Side</h2> <p><a 
  href="http://ellislab.com/codeigniter">CodeIgniter</a> is a powerful MVC 
framework that Brovine uses. Almost all of the data transfer is done using AJAX, 
which gives the user the effect of a seamless desktop application with few 
complete page reloads.</p> <p>Brovine"s MySQL data store is a highly joined set 
of tables that represent the complicated client data. Most queries require a 
join of four or five tables. Details about the database implementation are 
explained on <a href="/help/SQLSchema">the SQL schema help page</a>.</p> 
<p>Genetic data, which is represented by a series of CSV files, is uploaded 
using the <a href="http://www.uploadify.com/">Uploadify</a> plugin. Uploadify is 
a Flash plugin that provides safe, seamless upload of data files to a server 
with minimal effort required of the user. It also lets the user track the 
progress of the file uploads, upload multiple files at one time, and cancel any 
uploads if necessary. Brovine stores temporary files from Uploadify into the 
<code>/brovine/genedata-uploads</code> folder while the system converts the 
files into data usable by Brovine.</p> <p>The data itself is received from <a 
  href="/help/glossary#tess">TESS</a> as a set of Excel files. There is a set of 
example files <a href="/files/experiment-data.zip">downloadable as a zip 
  file</a>. Each gene that the researcher analyzes has its own set of 3 files 
which must all be uploaded to Brovine if the gene is to be committed into the 
system:</p> <ul> <li><strong>Job parameters</strong>: contains information about 
  the experiment conducted, and the gene; populates the experiment, 
  comparison_type, and gene tables.</li> <li><strong>Sequences</strong>: contains 
  information about the promoter sequence for the gene; populates the 
  promoter_sequence table</li> <li><strong>HITS–1</strong>: contains information 
  about the regulatory elements discovered in prior research; populates the 
  regulatory_sequences, factor_matches, and study_pages tables.</li> </ul> <h2 
  id="client-side">Client-Side</h2> <p>The following libraries are used to enhance 
the user experience:</p> <ul> <li><a href="http://jquery.com/">JQuery</a>: a 
  powerful Javascript client library</li> <li><a 
    href="http://www.datatables.net/">DataTables</a>: a JQuery plugin that provides 
  extensive table support</li> <li><a 
    href="http://loopj.com/jquery-tokeninput/">TokenInput</a>: a small but powerful 
  JQuery plugin which enhances text boxes. The plugin searches through a set of 
  pre-defined strings given a user input, which the user can then select.</li> 
  <li><a href="http://twitter.github.io/bootstrap/index.html">Bootstrap</a>: a 
  front-end HTML5 framework that makes web design simpler by offering basic styles 
  for tables, lists, navigation, layout, and more.</li> </ul> <p>Each user view is 
essentially a set of tables that allow the user to drill down to specific data 
points they want to see. For example, on the <a 
  href="/help/ViewDescriptions#tf_summary">Transcription Factor Summary</a> page, 
the user is interested in finding all genes in which a specific transcription 
factor occurs. So the first table lets the user select a transcription factor by 
name, and the second table shows all genes which have the selected factor.</p> 
<p>Each table is populated on the back end by a method in the ajax controller, 
which is located at <code>/brovine/application/controllers/ajax.php</code>. The 
DataTables library handles searching (via the box above each table), sorting, 
and filtering on the client side.</p> <p>There are several other features of the 
Transcription Factor Summary page that are worth mentioning. The “Filter by 
Regulation” box is an example of a TokenInput text box. The purpose of this text 
box is to allow users to filter their results by <a 
  href="/help/glossary#regulation">regulation</a>. As the user starts typing, the 
TokenInput library sends whatever prefix they have typed to the 
<code>getRegHints</code> method on the ajax controller, which attempts to match 
their prefix term with any of the regulation types in the database.</p> <h2 
  id="cachecontrol">Cache Control</h2> <p>Another feature of Brovine is the cache 
control mechanism. All static files (JS and CSS) have a timestamp appended to 
their name using the Apache <code>mod_rewrite</code> module. The timestamp is 
the last modify time on the file, so each time a static file is modified, the 
browser will think it is a brand new file and download the new version. This 
eliminates issues where cached versions of static files are used by the client's 
browser.</p> <h2 id="javascriptarchitecture">Javascript Architecture</h2> 
<p>Each view has its own javascript file - for example, the ExperimentHierarchy 
javascript code is held inside <code>experimentHierarchy.js</code>. However, 
there is also the <code>common.js</code> code which holds methods useful for all 
of the views. It contains the <code>updateSelectList</code> and 
<code>updateMultiSelectList</code> methods, which set up event handlers and 
handle the AJAX calls for each table.</p> <p>Here is a list of views and their 
corresponding Javascript code:</p> <ul> <li><strong>Experiment 
    Hierarchy</strong>: <code>experimentHierarchy.js</code></li> 
  <li><strong>Transcription Factor Search</strong>: <code>tfSearch.js</code></li> 
  <li><strong>Transcription Factor Subtract</strong>: <a href="#js_arch">See the 
    next section</a></li> <li><strong>Gene Summary</strong>: 
  <code>geneSummary.js</code></li> <li><strong>Transcription Factor 
    Summary</strong>: <code>tfSummary.js</code></li> <li><strong>Gene 
    Search</strong>: <code>geneSearch.js</code></li> <li><strong>Transcription 
    Factor Popularity</strong>: <code>tfPop.js</code></li> <li><strong>Frequent 
    Transcription Factors</strong>: <code>experimentHierarchy.js</code></li> </ul> 
<p>The file <code>scripture.js</code> is responsible for the local download 
functionality which is present on some Brovine views - the <a 
  href="help/ViewDescriptions#tf_summary">Transcription Factor Subtract page</a>, 
for example. Instead of generating a file on the server and sending it back to 
the user, this Javascript code generates a <code>data:octet-stream</code> link 
which lets the user extract the data without making another AJAX call.</p> 
<p>The file <code>upload.js</code> talks to the Uploadify flash software to 
notify the user about the status of file uploads on <a href="/Upload">the Upload 
  page</a>.</p> <h2 id="newjavascriptarchitecture">New Javascript 
  Architecture</h2> <p>The creators of Brovine recognized that the current 
Javascript code is a huge mess, but under the weight of deadlines found no 
reason to change the design. However, a significant effort has been made to 
create a system that was more reasonable. This effort is located in the 
<code>commonjs</code> folder in the Javascript folder. Currently, the 
Transcription Factor Subtract page is the only view to use the new Javascript 
architecture.</p> <p><b id="firstdiff"></b>This new architecture uses <a 
  href="http://wiki.commonjs.org/wiki/Modules/1.1.1">CommonJS Modules</a> and <a 
  href="https://github.com/LearnBoost/browserbuild">browserbuild</a> to create a 
modular Javascript system that greatly increases code reuse and maintainability 
within Brovine. Javascript files that are shared among pages belong in the 
<code>lib</code> folder, while view-specific files belong in the 
<code>init-pages</code> folder. Each Brovine view still has its own Javascript 
file, but the file size per page is much smaller. Browserbuild lets us 
concatenate and minify all necessary files into one unit, which reduces load 
time for the user.</p>

<h2>Server Configuration</h2>
<a href="#config"></a>

<p>Brovine is currently hosted on a VM managed by the Cal Poly Computer Science
department. Check with the CSL sysadmins to get access to the box via the shell.
On the VM is the usual LAMP stack plus Java:</p>

<ul>
  <li>MySQL.</li>
  <li>PHP.</li>
  <li>Apache.</li>
  <li>phpMyAdmin. <small>Unnecessary; simply for database manipulation.</small></li>
  <li>Java Runtime Client (JRE).</li>
</ul>

<p>The machine hosting Brovine needs to have a Java runtime client
installed to run the <a href="/help/FreqItemsetGenerator">Frequent Itemset
  Generator</a>. The generator runs as a standalone Java client that calculates
the most common transcription factors among the genes selected. The data
generated is displayed on the <a href="/help/PageDescriptions">Frequent
  Transcription Factors page</a>.</p>

<p>On the box, the server root is located at <code>/var/www/html</code>.</p>

<h3>PHP Configuration</h3>

<h3>Apache Configuration</h3>
<p>Brovine and CodeIgniter use the <code>mod_rewrite</code> package to edit incoming URLs. This
enables CodeIgniter to shorten the final URL that the user sees and uses to
access the service. It also enables Brovine to serve versioned CSS and JS
documents, which stops browsers from using outdated files.</p>

<h3>MySQL Configuration</h3>
<p>The MySQL configuration for Brovine is simple - just create the database
using <a href="/files/brovine_schema.sql">SQL schema</a> and import the <a href="/files/brovine_content.sql">database backup</a>. The username
password, server name, and port that are used for Brovine are stored in the
<code>passwd.php</code> file, which is not uploaded to the repository. There is
an example of the file in <a href="https://github.com/tcirwin/teambrovine/blob/master/README.md">the repository's README</a>.</p>

<h2>FAQ</h2>
</p>
<h3>General Questions</h3>
<p>
<strong>What are we looking to get out of this project?</strong> - We're really looking for the ability of something to compare "the list of stuff" from one gene to another (how strong of a match)
</p>
<h3>Genetics Questions</h3>
<p>
<strong>For our purposes, what is a gene?</strong> - A gene is the 2000 base pairs that we get, for our purposes (even though this is not actually the case, the 2000 base pairs are the promoter region in front of the gene)
</p>
<h3>Technical Questions</h3>
<ul>
  <li><strong>Could a factor have the same Beg, Sns, Len and a different Sequence?</strong> - No</li>
  <li><strong>Can there be a different beg/len for the opposite sns that would still match?</strong> - No</li>
  <li><strong>What are L factors used for?</strong> - These are different measures of the probability of this seq actually interacting with this factor.</li>
</ul>
