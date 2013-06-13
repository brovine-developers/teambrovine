<h1>Using Brovine</h1>

<h3>Basics</h3>
<p>To start using Brovine, you need to get an account. Currently, the registration
   for Brovine is closed, so an account must be created for you by an administrator.
   To get an account, contact the person in charge of Brovine.</p>

<p>After you get an account and log in, you will be presented with the
   <a href="/help/ViewDescriptions#experiment_hierarchy">Experiment Hierarchy page</a>, as well as a
   navigation bar at the top of the page. Click the <em>Navigation</em> link
   to see a list of <em>views</em> available. Each <em>view</em> is explained on the
   <a href="/help/ViewDescriptions#experiment_hierarchy">View Descriptions
   help page</a>.</p>

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
   <li><strong>Export Buttons:</strong> Tables that have an "Export" button below
       them can be exported as a CSV file.</li>
</ul>

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
   upload any data about that gene.</p>

<a name="account_management"></a>
<h2>Account Management</h2>
Each user of Brovine must have an account in order to access any views.

<a name="privilege_types"></a>
<h3>Privilege Types</h3>
<ul>
   <li><strong>Admin:</strong> This type of user can browse, edit, and hide data
       in Brovine. In the future, this type of account will be able
       to add and remove other user accounts.</li>
   <li><strong>Modify:</strong> This type of user can browse the data in Brovine
       and can also hide or edit data using the <a href="#experiment_hierarchy">
       Experiment Hierarchy page</a>. They can also add new data using the
       <a href="/Upload">Upload page</a>.</li>
   <li><strong>Read:</strong> This type of user can do nothing except browse the
       data available in Brovine.</li>
</ul>

<a name="add_remove_users"></a>
<h3>Adding and Removing User Accounts</h3>
<p>Currently, user accounts can only be added or removed by Brovine's software developer.</p>

<a name="settings"></a>
<h3>Changing your Password or Display Name</h3>
<p>After logging in, a user can change his or her password or display name using
   the <a href="/auth/settings">Settings page</a>. This page is also linked in the
   menu under your display name.</p>
