<a href="/help"><p><i class="icon-arrow-left"></i> Back to Help Home</a>

<h2>View Descriptions</h2>

<div class="thumbnail use-case">
   <div class="caption left">
      <a name="experiment_hierarchy"></a>
      <h3>Experiment Hierarchy</h3>
      <p><strong>How to use this view:</strong></p>

      <ol>
         <li>Select a Species from <span class="badge badge-warning">1</span>.
             All comparisons which are about the selected species will populate
             the next table.</li>
         <li>Select a Comparison from <span class="badge badge-warning">2</span>.
             All experiments which use the selected comparison will be present
             in the next table.</li>
         <li>Select an Experiment from <span class="badge badge-warning">3</span>.
             All genes in the selected experiment will be present in the next
             table.</li>
         <li>Select a Gene from <span class="badge badge-warning">4</span>
             All of the gene's transcription factors will appear in the next
             table.</li>
         <li>Select a Transcription Factor from <span class="badge badge-warning">5</span>.
             All regulatory sequences which match the transcription factor
             selected will appear in the next table.</li>
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

      <p><strong>How to use this view:</strong></p>

      <ol>
         <li>Select one or more Species from <span class="badge badge-warning">1
             </span>. All comparisons for any selected species will populate
             the next table.</li>
         <li>Select one or more Comparisons from <span class="badge badge-warning">2</span>.
             All experiments which use any of the selected comparisons will be present
             in the next table.</li>
         <li>Select one or more Experiments from <span class="badge badge-warning">3</span>.
             All genes in any of the selected experiments will be present in the next
             table.</li>
         <li>Select one or more Genes from <span class="badge badge-warning">4</span>
             All transcription factors present in any gene selected will appear
             in the next table.</li>
         <li>Select a Transcription Factor from <span class="badge badge-warning">5</span>.
             All regulatory sequences which match the transcription factor
             selected will appear in the next table.</li>
         <li>Select a Regulatory Sequence from <span class="badge badge-warning">6</span>.
             The <em>Sequence Info</em> section will appear, with all information
             about the sequence you've selected, as well as similar sequences and
             matching factors in <span class="badge badge-warning">7</span> and
             <span class="badge badge-warning">8</span></li>
      </ol>
   </div>

   <img src="/images/examples/tf-search.png" />
</div>

<a name="tf_subtract"></a>
<div class="thumbnail use-case">
   <div class="caption left">
      <h3>Transcription Factor Subtract</h3>
      <p>Finds transcription factors that are in any gene selected from
         <span class="badge badge-warning">4</span> and which are not in any
         gene selected in <span class="badge badge-warning">5</span>. For example,
         imagine gene A has transcription factors M, N, and O, while gene B has
         transcription factors O, P, and Q. If A was selected in
         <span class="badge badge-warning">4</span> and B selected in
         <span class="badge badge-warning">5</span>, then
         <span class="badge badge-warning">6</span> would show M and N only.

      <p><strong>How to use this view:</strong></p>

      <ol>
         <li>Select one or more Species from <span class="badge badge-warning">1
             </span>. All comparisons for any selected species will populate
             the next table.</li>
         <li>Select one or more Comparisons from <span class="badge badge-warning">2</span>.
             All experiments which use any of the selected comparisons will be present
             in the next table.</li>
         <li>Select one or more Experiments from <span class="badge badge-warning">3</span>.
             All genes in any of the selected experiments will be present in the next
             table.</li>
         <li>Select one or more Genes from <span class="badge badge-warning">4
             </span>. The transcription factors present in these genes will be
             included in <span class="badge badge-warning">6</span>.</li>
         <li>Select one or more Genes from <span class="badge badge-warning">5
             </span>. The transcription factors present in these genes will be
             excluded, by name, from <span class="badge badge-warning">6</span>.</li>
      </ol>
   </div>

   <img src="/images/examples/tf-subtract.png" />
</div>

<a name="gene_summary"></a>
<div class="thumbnail use-case">
   <div class="caption right">
      <h3>Gene Summary</h3>
      <p><strong>How to use this view:</strong></p>

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

      <p><strong>How to use this view:</strong></p>

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

      <p><strong>How to use this view:</strong></p>

      <ol>
         <li>Select one or more Species from <span class="badge badge-warning">1
             </span>. All comparisons for any selected species will populate
             the next table.</li>
         <li>Select one or more Comparisons from <span class="badge badge-warning">2</span>.
             All experiments which use any of the selected comparisons will be present
             in the next table.</li>
         <li>Select one or more Experiments from <span class="badge badge-warning">3</span>.
             All genes in any of the selected experiments will be present in the next
             table.</li>
         <li>Select one or more Transcription Factors from
             <span class="badge badge-warning">4</span>. Genes which have any
             of the selected transcription factors will be shown in the next
             table.</li>
         <li>Select one or more Genes from <span class="badge badge-warning">5
             </span>. Experiments which contain <em>all</em> of the genes
             selected will be shown in <span class="badge badge-warning">6
             </span>.</li>
      </ol>
   </div>

   <img src="/images/examples/gene-search.png" />
</div>

<a name="tf_popularity"></a>
<div class="thumbnail use-case">
   <div class="caption left">
      <h3>Transcription Factor Popularity</h3>

      <p><strong>How to use this view:</strong></p>

      <ol>
         <li>Select a Species from <span class="badge badge-warning">1</span>.
             All comparisons which are about the selected species will populate
             the next table.</li>
         <li>Select a Comparison from <span class="badge badge-warning">2</span>.
             All experiments which use the selected comparison will be present
             in the next table.</li>
         <li>Select an Experiment from <span class="badge badge-warning">3</span>.
             All genes in the selected experiment will be present in the next
             table.</li>
         <li>Select a Transcription Factor from
             <span class="badge badge-warning">4</span>. All occurrences of the
             transcription factor selected, from any gene in the experiment
             selected, will be displayed in
             <span class="badge badge-warning">5</span>.
      </ol>
   </div>

   <img src="/images/examples/tf-popularity.png" />
</div>

<a name="freq_transcription_factors"></a>
<div class="thumbnail use-case clearfix">
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
      <p><strong>Note:</strong> Select the Minimum and Maximum percentages
         wisely. Choosing too wide of a range, or selecting maximum greater than
         96%, may not yield any results. Try to choose a range close
         to the default (85 - 95%).</p>
      <p><strong>How to use this view:</strong></p>

      <ol>
         <li>If <span class="badge badge-warning">1</span> is loading data, wait
             until data is loaded. If it has been loading for a long time, try
             refreshing the page.</li>
         <li>Select minimum and maximum percentage above
             <span class="badge badge-warning">1</span>, then click Go.</li>
         <li>All sets of transcription factors which are present in a percentage
             of genes within the range will be displayed in
             <span class="badge badge-warning">1</span>.</li>
      </ol>
   </div>

   <img src="/images/examples/freq-transfacs.png" />
</div>
   <div class="clear"></div>

<a href="/help"><p><i class="icon-arrow-left"></i> Back to Help Home</a>
