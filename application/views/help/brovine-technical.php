<h1>Brovine Technical Description</h1>

<h2>Overview</h2>
<a href="#overview"></a>
<p>The Brovine gene database is built upon the CodeIgniter framework on the backend, an MVC framework written in PHP. This runs on an Apache HTTP server, and it uses a MySQL database to store all of the genetic data. On the front end, the application is entirely in Javascript, using several open source projects, including JQuery, DataTables, and TokenInput.</p>
<p>Each page, which represents a “view” which the customers find useful, contains tables that display the genetic data to the user, as well as let the user drill down to more specific data. For example, on each page there are tables which show the species and experiments the customers have uploaded to the application. Customers can select one or more species, and Brovine will filter the experiment table to show only experiments with the selected species.</p>

<h2>Front-end Description</h2>
<a href="#frontend"></a>

<h2>Back-end Description</h2>
<a href="#backend"></a>

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
