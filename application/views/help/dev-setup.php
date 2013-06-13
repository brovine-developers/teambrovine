<h1>Development Environment Setup</h1>
<p>Development on a local machine is required for those who are performing major
changes to the site. Using the VM to test is not acceptable for a live system,
where users could be using the features you're testing! Follow these steps to
set up Brovine on your local box.</p>

<h2>Prerequisites</h2>
<ul>
   <li>A Linux or Mac machine. <small>Windows will probably work, but I haven't tried.</small></li>
   <li>A working LAMP stack. For Mac users, I've heard <a href="http://www.mamp.info/en/index.html">MAMP</a>
       is a good solution.</li>
   <li>The Java Runtime Environment (JRE) installed.</li>
</ul>

<h2>Install Brovine</h2>
<ol>
   <li>Clone the <a href="https://github.com/tcirwin/teambrovine">code for Brovine</a>
       into your development directory.
       <pre>git clone https://github.com/tcirwin/teambrovine</pre></li>
   <li>Edit Apache's <code>httpd.conf</code> to support CodeIgniter's clean URLs.
       You have to enable <code>mod_rewrite</code>, which lets you define rules
       in <code>.htaccess</code> that modify incoming request URLs:
       <pre>LoadModule rewrite_module /usr/lib/apache2/modules/mod_rewrite.so</pre></li>
   <li>Start Apache and MySQL. Check that you can reach the log in page of Brovine.
       If you can't, try changing the file attributes and group. Each folder in Brovine
       should be in the group which the Apache HTTPD user is in. This is
       generally <code>_www</code>:
       <pre>chown -R :_www /brovine</pre>
       All folders should have <code>rwx</code> access for group; files at least
       <code>r</code>:
       <pre>chmod -R g+rwx /brovine</pre></li>
   <li>Create the <code>brovine</code> database in MySQL.</li>
   <li>Make sure at least one user has the following privileges to the database:
       <code>insert, update, delete, select, index</code> (preferrably not root)</li>
   <li>Copy the sample <code>passwd.php</code> file from <a href="https://github.com/tcirwin/teambrovine/blob/master/README.md">the repository's README</a>
       into the <code>brovine/application/config</code> directory. Edit the
       file to match the settings you used in the previous step.</li>
   <li>Get the <a href="/files/brovine_schema.sql">SQL schema</a> and the 
       <a href="/files/brovine_content.sql">database backup</a>. Import both
       into the <code>brovine</code> database you created earlier. Check that
       all of the required tables are present and populated (see the
       <a href="/help/SQLSchema">SQL schema description</a> for more information).</li>
</ol>

<h2>Install the <a href="/help/FreqItemsetGen#overview">Frequent Itemset Generator</a></h2>
<p>This standalone service generates data for the <a href="/help/PageDescriptions">Frequent
Transcription Factors page</a>.</p>

<ol>
   <li>Clone the <a href="http://github.com/tcirwin/freq-itemset-gen">frequent itemset generator code</a>.
       into your development directory.
       <pre>git clone https://github.com/tcirwin/freq-itemset-gen</pre></li>
   <li>Start the service:
       <pre>make start</pre></li>
</ol>
