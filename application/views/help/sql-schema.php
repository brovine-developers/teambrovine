<h1>Brovine's SQL Schema</h1>
<p>Table and column descriptions and an ER diagram of the database. All tables
   have an auto-increment ID field which were omitted from the column descriptions.</p>
<p>Download <a href="/files/brovine_schema.sql">the Brovine database schema</a>.</p>

<div class="row">
   <div class="span6">
      <table class="table table-striped">
         <thead>
            <tr>
               <th>Table Name</th>
               <th>Used For</th>
            </tr>
         </thead>

         <tbody>
            <tr>
               <td><a href="#comparison_types">comparison_types</a></td>
               <td>Stores all differentiation types the researchers are studying. The key
                   is species, celltype, transition.</td>
            </tr>

            <tr>
               <td><a href="#experiments">experiments</a></td>
               <td>Stores each experiment that the researcher performs, each of which
                   has a distinct comparison type and species. The key is a automatically
                   assigned experiment id.</td>
            </tr>

            <tr>
               <td><a href="#genes">genes</a></td>
               <td>Stores a gene that the researcher studies, which can be present in
                   multiple experiments. Keyed by gene name.</td>
            </tr>

            <tr>
               <td><a href="#regulatory_sequences">regulatory_sequences</a></td>
               <td>Stores a sequence of nucleotides that affect the expression of a
                   specific gene. See <a href="/help/Glossary#regulatory_sequence">
                   the glossary entry for more details.</a></td>
            </tr>

            <tr>
               <td><a href="#factor_matches">factor_matches</a></td>
               <td>Stores transcription factors that other researchers have studied. Each
                   transcription factor can be associated with multiple regulatory
                   sequences, and each regulatory sequence can match multiple factor
                   matches.</td>
            </tr>

            <tr>
               <td><a href="#study_pages">study_pages</a></td>
               <td>Stores research paper references that the factor matches were
                   retrieved from. Not currently used in the Brovine system.</td>
            </tr>

            <tr>
               <td><a href="#promoter_sequences">promoter_sequences</a></td>
               <td>Stores the sequence of nucleotides which contain all possible
                   regulatory sequences for a specific gene.</td>
            </tr>

            <tr>
               <td>apriori_staging</td>
               <td>Stores temporary data for the <a href="/help/FreqItemsetGen">
                   Frequent Itemset Generator</a>.</td>
            </tr>

            <tr>
               <td><a href="#users">users</a></td>
               <td>Stores user data for Brovine - usernames, password hashes,
                   etc.</td>
            </tr>
         </tbody>
      </table>
   </div>

   <div class="span6">
      <h2>ER Diagram</h2>
      <img src="/files/er-diagram.png" alt="an entity-relationship model of the data
      modeled with Brovine." />
   </div>
</div>

<h2>Table Schemas</h2>
<div class="row">
   <div class="span6">
      <a name="comparison_types"></a>
      <h3>Comparison Types</h3>
      <table class="table table-striped">
         <thead><tr>
            <th>Column Name</th>
            <th>Type</th>
            <th>Description</th>
         </tr></thead>

         <tbody>
            <tr>
               <td>species</td>
               <td>varchar</td>
               <td>The species that the researcher is studying.</td>
            </tr>

            <tr>
               <td>celltype</td>
               <td>varchar</td>
               <td>The differentiation (from one cell type to another) that the
                   researcher is studying.</td>
            </tr>
         </tbody>
      </table>

      <a name="genes"></a>
      <h3>Genes</h3>
      <table class="table table-striped">
         <thead><tr>
            <th>Column Name</th>
            <th>Type</th>
            <th>Description</th>
         </tr></thead>

         <tbody>
            <tr>
               <td>genename</td>
               <td>varchar</td>
               <td>the name of this gene.</td>
            </tr>

            <tr>
               <td>chromosome</td>
               <td>int</td>
               <td>the chromosome which this gene is on.</td>
            </tr>

            <tr>
               <td>start</td>
               <td>int</td>
               <td>the start nucleotide of this gene on the chromosome.</td>
            </tr>

            <tr>
               <td>end</td>
               <td>int</td>
               <td>the end nucleotide of this gene on the chromosome.</td>
            </tr>

            <tr>
               <td>experimentid</td>
               <td>int</td>
               <td>the experiment that this gene was studied on.</td>
            </tr>

            <tr>
               <td>geneabbrev</td>
               <td>varchar</td>
               <td>the abbreviation of the gene name.</td>
            </tr>

            <tr>
               <td>regulation</td>
               <td>varchar</td>
               <td>the expression of the gene in the experiment.</td>
            </tr>
         </tbody>
      </table>

      <a name="factor_matches"></a>
      <h3>Factor Matches</h3>
      <table class="table table-striped">
         <thead><tr>
            <th>Column Name</th>
            <th>Type</th>
            <th>Description</th>
         </tr></thead>

         <tbody>
            <tr>
               <td>seqid</td>
               <td>varchar</td>
               <td>indicates the sequence which this factor matches.</td>
            </tr>

            <tr>
               <td>study</td>
               <td>varchar</td>
               <td>the biological study which this factor match was obtained
                   from (indirectly through TESS).</td>
            </tr>

            <tr>
               <td>transfac</td>
               <td>varchar</td>
               <td>the name of this factor</td>
            </tr>

            <tr>
               <td>la</td>
               <td>double</td>
               <td>a factor quality indicator (how reliable the relationship
                   between this factor and its related sequence are).</td>
            </tr>

            <tr>
               <td>la_slash</td>
               <td>double</td>
               <td>a factor quality indicator.</td>
            </tr>

            <tr>
               <td>lq</td>
               <td>double</td>
               <td>a factor quality indicator.</td>
            </tr>

            <tr>
               <td>lq</td>
               <td>double</td>
               <td>a factor quality indicator.</td>
            </tr>

            <tr>
               <td>ld</td>
               <td>double</td>
               <td>a factor quality indicator.</td>
            </tr>

            <tr>
               <td>lpv</td>
               <td>double</td>
               <td>a factor quality indicator.</td>
            </tr>

            <tr>
               <td>sc</td>
               <td>double</td>
               <td>a factor quality indicator.</td>
            </tr>

            <tr>
               <td>sm</td>
               <td>double</td>
               <td>a factor quality indicator.</td>
            </tr>

            <tr>
               <td>spv</td>
               <td>double</td>
               <td>a factor quality indicator.</td>
            </tr>

            <tr>
               <td>ppv</td>
               <td>double</td>
               <td>a factor quality indicator.</td>
            </tr>
         </tbody>
      </table>
   </div>

   <div class="span6">
      <a name="experiments"></a>
      <h3>Experiments</h3>
      <table class="table table-striped">
         <thead><tr>
            <th>Column Name</th>
            <th>Type</th>
            <th>Description</th>
         </tr></thead>

         <tbody>
            <tr>
               <td>label</td>
               <td>varchar</td>
               <td>The experimenter-designated name for the experiment.</td>
            </tr>

            <tr>
               <td>comparisontypeid</td>
               <td>int</td>
               <td>Specifies the comparison type and species that this
                   experiment is performed on.</td>
            </tr>

            <tr>
               <td>tessjob</td>
               <td>varchar</td>
               <td>The TESS job number assigned to the experiment's results.</td>
            </tr>

            <tr>
               <td>experimenter_email</td>
               <td>varchar</td>
               <td>Email address of the experimenter who performed the
                   experiment.</td>
            </tr>
         </tbody>
      </table>

      <a name="regulatory_sequences"></a>
      <h3>Regulatory Sequences</h3>
      <table class="table table-striped">
         <thead><tr>
            <th>Column Name</th>
            <th>Type</th>
            <th>Description</th>
         </tr></thead>

         <tbody>
            <tr>
               <td>beginning</td>
               <td>int</td>
               <td>start nucleotide on the promoter sequence where this
                   regulatory sequence begins.</td>
            </tr>

            <tr>
               <td>length</td>
               <td>int</td>
               <td>number of nucleotides in this regulatory sequence.</td>
            </tr>

            <tr>
               <td>sense</td>
               <td>char</td>
               <td>direction of regulation of this sequence</td>
            </tr>

            <tr>
               <td>geneid</td>
               <td>int</td>
               <td>gene which this sequence regulates.</td>
            </tr>
         </tbody>
      </table>

      <a name="promoter_sequences"></a>
      <h3>Promoter Sequences</h3>
      <table class="table table-striped">
         <thead><tr>
            <th>Column Name</th>
            <th>Type</th>
            <th>Description</th>
         </tr></thead>

         <tbody>
            <tr>
               <td>geneid</td>
               <td>int</td>
               <td>the gene ID of this promoter sequence.</td>
            </tr>

            <tr>
               <td>sequence</td>
               <td>varchar</td>
               <td>the 2000 base pair nucleic acid sequence that is the
                   <a href="/help/Glossary#promoter_sequence">promoter region</a>
                   for this gene.</td>
            </tr>
         </tbody>
      </table>

      <a name="study_pages"></a>
      <h3>Study Pages</h3>
      <table class="table table-striped">
         <thead><tr>
            <th>Column Name</th>
            <th>Type</th>
            <th>Description</th>
         </tr></thead>

         <tbody>
            <tr>
               <td>pageno</td>
               <td>varchar</td>
               <td>the page identifier for the study.</td>
            </tr>

            <tr>
               <td>seqid</td>
               <td>int</td>
               <td>the sequence which this page is referenced in.</td>
            </tr>
         </tbody>
      </table>


      <a name="users"></a>
      <h3>Users</h3>
      <table class="table table-striped">
         <thead><tr>
            <th>Column Name</th>
            <th>Type</th>
            <th>Description</th>
         </tr></thead>

         <tbody>
            <tr>
               <td>username</td>
               <td>varchar</td>
               <td>login name for the user.</td>
            </tr>

            <tr>
               <td>password</td>
               <td>varchar</td>
               <td>the SHA1 hash of the user's password.</td>
            </tr>

            <tr>
               <td>display_name</td>
               <td>varchar</td>
               <td>when the user is logged in, this name is displayed in the
                   navigation bar.</td>
            </tr>

            <tr>
               <td>privileges</td>
               <td>int</td>
               <td>indicates what privileges the user have. Read = 0, Write = 10,
                   Administrator = 20. For more information on privileges, see
                   <a href="/help/AccountManagement#privileges">the account
                   management page</a>.</td>
            </tr>
         </tbody>
      </table>
   </div>
</div>

