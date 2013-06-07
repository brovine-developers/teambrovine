<h1>Using Brovine</h1>

<h3>Basics</h3>
<p>To start using Brovine, you need to get an account. All users need to log in
   to protect Brovine from malicious users. To get an account, contact the
   person in charge of Brovine.</p>

<p>After you get past the login page, you will be presented with the
   <a href="#experiment_hierarchy">Experiment Hierarchy page</a>, as well as a
   navigation bar at the top of the page. Click the <em>Navigation</em> link
   to see a list of <em>views</em> available. Each <em>view</em> is explained
   <a href="#experiment_hierarchy">later on in this document</a>.</p>

<p>Clicking on your display name in the navigation bar will display a menu of
   options that affect your user account. If you have sufficient access to
   Brovine, the <a href="#editing_data">Upload</a> link will also appear in this
   menu. The settings link on this menu allows you to change your password or
   your display name. Finally, the log out link is in this menu.</p>

<h3>Navigating Through Data</h3>
<p>Most pages contain many <em>tables</em> where genetic data is displayed.
   These tables can be <em>clicked</em> with the mouse to
   select rows of data that you would like to see more about. For example,
   on the <a href="#experiment_hierarchy">Experiment Hierarchy page</a> if you
   select <em>Bovine</em> in the <em>Species</em> table, the <em>Comparison</em>
   table next to it will be populated only with comparison types involving
   Bovine species. Additionally, some tables in Brovine allow the user to select
   multiple table rows at the same time. In this case, hold down Ctrl (Windows)
   or Command (Mac) to select multiple rows of data.</p>

<p>In general, the <em>flow</em> of each page is from left to right, then top
   to bottom. This means that to use each <em>view</em>, you must start with the
   <em>table</em> in the top-left corner and work right and then down.</p>

<h3>View Features</h3>
<ul>
   <li><strong>Quality Filter:</strong> Some pages, like the 
       <a href="#tf_search">Transcription Factor Search page</a>, contain some
       extra search options for regulatory sequences. These search options are
       labeled <em>Regulatory Sequence Filter Options</em>, and they let you
       search using minimum and maximum values for quality filters (La, La/, 
       Lq, Ld), promoter sequence position, and sense.</li>
   <li><strong>Table Search:</strong> Each <em>table</em> in Brovine is equipped
       with a <em>search box</em> that searches through all of the data in the
       table, displaying only rows which contain each word you search for. The
       <em>search box</em> is located right above each table.</li>
   <li><strong>Regulation Filter:</strong> Some pages, such as the
       <a href="#gene_summary">Gene Summary page</a>, let you filter genes by
       regulation type. This box will appear above the table that it filters,
       next to a "Filter by Regulation" label.</li>
</ul>


<div class="thumbnail use-case">
   <div class="caption left">
      <a name="experiment_hierarchy"></a>
      <h3>Experiment Hierarchy</h3>
      <p><strong>The <em>flow</em> of this page proceeds as follows:</strong></p>

      <ol>
         <li>Select a Species from <span class="badge badge-warning">1</span></li>
         <li>Select a Comparison from <span class="badge badge-warning">2</span></li>
         <li>Select an Experiment from <span class="badge badge-warning">3</span></li>
         <li>Select a Gene from <span class="badge badge-warning">4</span></li>
         <li>Select a Transcription Factor from <span class="badge badge-warning">5</span></li>
         <li>Select a Regulatory Sequence from <span class="badge badge-warning">6</span>.
             The <em>Sequence Info</em> section will appear, with all information
             about the sequence you've selected, as well as similar sequences and
             matching factors in <span class="badge badge-warning">7</span> and
             <span class="badge badge-warning">8</span></li>
      </ol>

      <p>There is a <em>regulation filter</em> for <span class="badge badge-warning">
         4</span> and a <em>quality filter</em> for
         <span class="badge badge-warning">6</span>.</p>

      <p> On this page, comparisons,
         experiments, genes, and regulatory sequences can be edited using the
         "Edit" and "Hide" buttons. Additionally, a user can choose to see
         previously hidden rows ("Show Hidden" above
         <span class="badge badge-warning">1</span>) or color edited and hidden
         rows differently than the rest ("Color Edited and Hidden"). Edited rows
         will show up yellow and hidden rows will show up red.</p>
   </div>

   <img src="/images/examples/experiment-hierarchy.png" />
</div>

<a name="tf_search"></a>
<div class="thumbnail use-case">
   <div class="caption right">
      <h3>Transcription Factor Search</h3>
   </div>

   <img src="/images/examples/gene-summary.png" />
</div>

<a name="tf_subtract"></a>
<div class="thumbnail use-case">
   <div class="caption left">
      <h3>Transcription Factor Subtract</h3>
   </div>

   <img src="/images/examples/gene-summary.png" />
</div>

<a name="gene_summary"></a>
<div class="thumbnail use-case">
   <div class="caption right">
      <h3>Gene Summary</h3>
      <p><strong>The <em>flow</em> of this page proceeds as follows:</strong></p>

      <ol>
         <li>Select a Gene from <span class="badge badge-warning">1</span>.</li>
         <li>All experiments with the Gene that was selected will appear in
             <span class="badge badge-warning">2</span>.</li>
      </ol>
   </div>

   <img src="/images/examples/gene-summary.png" />
</div>

<a name="tf_summary"></a>
<div class="thumbnail use-case">
   <div class="caption left">
      <h3>Transcription Factor Summary</h3>
      <p><strong>The <em>flow</em> of this page proceeds as follows:</strong></p>

      <ol>
         <li>Select a Transcription Factor from
             <span class="badge badge-warning">1</span>.</li>
         <li>All genes with the Transcription Factor that was selected
             will appear in <span class="badge badge-warning">2</span>.</li>
      </ol>
   </div>

   <img src="/images/examples/tf-summary.png" />
</div>

<a name="gene_search"></a>
<div class="thumbnail use-case">
   <div class="caption right">
      <h3>Gene Search</h3>
   </div>

   <img src="/images/examples/tf-summary.png" />
</div>

<a name="tf_popularity"></a>
<div class="thumbnail use-case">
   <div class="caption left">
      <h3>Transcription Factor Popularity</h3>
   </div>

   <img src="/images/examples/tf-summary.png" />
</div>

<a name="freq_transcription_factors"></a>
<div class="thumbnail use-case">
   <div class="caption right">
      <h3>Frequent Transcription Factors</h3>
      <p>This view allows users of Brovine to discover the most commonly
         occurring transcription factors or sets of transcription factors in
         genes. For example, the factors AP-2alphaA and AP-2alphaB occur together
         in approximately 86% of all genes currently in the database. You can
         select the minimum and maximum percentage of genes a set can show up
         in to limit the results of the search. Upon reaching this <em>view</em>,
         <span class="badge badge-warning">1</span> will have a loading spinner,
         indicating that the data is being loaded.
      <p><strong>Note:</strong> Select the Minimum and Maximum support counts
         wisely. Choosing too wide of a range, or selecting maximum &gt; 96%,
         may not yield any results from the view. Try to choose a range close
         to the default (85 - 95%).</p>
      <p><strong>The <em>flow</em> of this page proceeds as follows:</strong></p>

      <ol>
         <li>Select a Transcription Factor from
             <span class="badge badge-warning">1</span>.</li>
         <li>All genes with the Transcription Factor that was selected
             will appear in <span class="badge badge-warning">2</span>.</li>
      </ol>
   </div>

   <img src="/images/examples/freq-transfacs.png" />
</div>
   <div class="clear"></div>

<a name="editing_data"></a>
<h3>Editing and Adding Data</h3>
<p>To edit or hide data in Brovine, see the <a href="#experiment_hierarchy">
Experiment Hierarchy page</a>.</p>

<p><strong>Adding Data</strong> to Brovine is accomplished with the <em>Upload</em> button under your user menu (click your display name in the navigation
   bar to access this menu). Once on the Upload Data page, click the "Select
   Files" link to choose files to upload into Brovine. To select multiple files,
   hold down the Shift key and select another file after selecting the first one.
   Currently, only CSV spreadsheets from the TESS system are supported. Be sure
   to include all three TESS files related to each gene, or Brovine will not
   upload any data about that gene.
