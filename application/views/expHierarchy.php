<div class="row">
   <div class="span12">
      <h1>Experiment Hierarchy</h1>
      <p>
         Begin by selecting a species. 
      </p>
   </div>

</div>

<div class="row ruleRow">
   <div class="span2 selector"> 
      <table class="table table-striped table-bordered" id="speciesList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
   <div class="span4 selector">
      <table class="table table-striped table-bordered" id="comparisonList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
   <div class="span6 selector">
      <table class="table table-striped table-bordered" id="experimentList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
</div>
<? // No editing or deleting Species / Comparisons.
/*
<div class="row ruleRow">
   <div class="span2">
      With selected species:<br>
      <a class="btn btn-warning disabled" id="editSpecies"><i class="icon-pencil icon-white"></i> Edit</a>
      <a class="btn btn-danger disabled" id="hideSpecies"><i class="icon-minus-sign icon-white"></i> Hide</a>
   </div>
   <div class="span4">
      With selected comparison:<br>
      <a class="btn btn-warning disabled" id="editComparison"><i class="icon-pencil icon-white"></i> Edit</a>
      <a class="btn btn-danger disabled" id="hideComparison"><i class="icon-minus-sign icon-white"></i> Hide</a>
   </div>
   <div class="span6">
      With selected experiment:<br>
      <a class="btn btn-warning disabled" id="editExperiment"><i class="icon-pencil icon-white"></i> Edit</a>
      <a class="btn btn-danger disabled" id="hideExperiment"><i class="icon-minus-sign icon-white"></i> Hide</a>
   </div>
</div>
 */
?>
<div class="row">
   <div class="span8">
         <div class="row" id="geneFilterOptions">
            <label class="radio span2">
               <input type="radio" name="regulationRadio" value="all" checked> Show All Genes
            </label>
            <label class="radio span2">
               <input type="radio" name="regulationRadio" value="up"> Up Regulated Only
            </label>
            <label class="radio span2">
               <input type="radio" name="regulationRadio" value="down"> Down Regulated Only
            </label>
         </div>
   </div>
</div>
<div class="row">
   <div class="span8">
      <table class="table table-striped table-bordered" id="geneList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
   <div class="span4">
      <table class="table table-striped table-bordered" id="factorList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
</div>
<div class="row ruleRow">
   <div class="span8">
      <a class="btn btn-warning disabled" id="editGene"><i class="icon-pencil icon-white"></i> Edit Gene</a>
      <a class="btn btn-danger disabled" id="hideGene"><i class="icon-minus-sign icon-white"></i> Hide Gene</a>
   </div>
   <div class="span4">
      <a class="btn btn-warning disabled" id="editFactor"><i class="icon-pencil icon-white"></i> Edit Factor</a>
      <a class="btn btn-danger disabled" id="hideFactor"><i class="icon-minus-sign icon-white"></i> Hide Factor</a>
   </div>
</div>
<div class="row">
   <div class="span12">
      <div class="well">
         <h3>Regulatory Sequence Filter Options</h3>
<? /* TODO: Add input type="number" if you feel like setting up
the event listeners for it. Need to get click, keypress, 
and scroll. */ ?>
         <div class="row" id="sequenceFilterOptions">
            <div class="span1">
               <label>Min La<br>
                  <input type="text" class="span1" id="minla">
               </label>
            </div>
            <div class="span1">
               <label>Min La/<br>
                  <input type="text" class="span1" id="minlaslash">
               </label>
            </div>
            <div class="span1">
               <label>Min Lq<br>
                  <input type="text" class="span1" id="minlq">
               </label>
            </div>
            <div class="span1">
               <label>Max Ld<br>
                  <input type="text" class="span1" id="maxld">
               </label>
            </div>
            <div class="span1 offset1">
               <label>Min Beg<br>
                  <input type="text" class="span1" id="minbeg">
               </label>
            </div>
            <div class="span1">
               <label>Max Beg<br>
                  <input type="text" class="span1" id="maxbeg">
               </label>
            </div>
            <div class="span3 offset1 form-inline">
               <div class="row">
                  <div class="span1">Sense</div>
               </div>
               <div class="row" id="senseFilters">
                  <div class="span1">
                  <label class="radio">
                     <input type="radio" name="senseFilter" value="all" checked>
                     All
                  </label>
                  </div>
                  <div class="span1">
                  <label class="radio">
                     <input type="radio" name="senseFilter" value="N">
                     N
                  </label>
                  </div>
                  <div class="span1">
                  <label class="radio">
                     <input type="radio" name="senseFilter" value="R">
                     R
                  </label>
                  </div>
               </div>
            </div>
         </div>
      </div>
      
   </div>
</div>
<div class="row">
   <div class="span12">
      <table class="table table-striped table-bordered" id="sequenceList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
</div>
<div class="row ruleRow">
   <div class="span12">
      <a class="btn btn-warning disabled" id="editSequence"><i class="icon-pencil icon-white"></i> Edit Sequence</a>
      <a class="btn btn-danger disabled" id="hideSequence"><i class="icon-minus-sign icon-white"></i> Hide Sequence</a>
   </div>
</div>
<div class="row" id="sequenceInfo">
</div>

<!-- Here be modals. -->
<div class="modal hide fade" id="editGeneModal">
   <form class="form-horizontal">
      <fieldset>
         <legend>Edit Gene</legend>
         <div class="control-group">
            <label for="genenameInput" class="control-label">Name</label>
            <div class="controls">
               <input id="genenameInput">
            </div>
         </div>
         <div class="control-group">
            <label for="geneabbrevInput" class="control-label">Abbreviation</label>
            <div class="controls">
               <input id="geneabbrevInput">
            </div>
         </div>
         <div class="control-group">
            <label for="genechromosomeInput" class="control-label">Chromosome</label>
            <div class="controls">
               <input id="genechromosomeInput">
            </div>
         </div>
         <div class="control-group">
            <label for="genestartInput" class="control-label">Start</label>
            <div class="controls">
               <input id="genestartInput">
            </div>
         </div>
         <div class="control-group">
            <label for="geneendInput" class="control-label">End</label>
            <div class="controls">
               <input id="geneendInput">
            </div>
         </div>
         <div class="control-group">
            <label class="control-label">Regulation</label>
            <div class="controls">
               <label class="radio">
                  <input type="radio" id="generegulationInputDown" name="editGeneRegulation" value="down">
                  Down
               </label>
               <label class="radio">
                  <input type="radio" id="generegulationInputUp" name="editGeneRegulation" value="up">
                  Up
               </label>
            </div>
         </div>
         <div class="control-group">
            <label class="control-label">Last Edited</label>
            <div class="controls">
               <label style="padding-top: 5px" id="geneLastEdited"></label>
            </div>
         </div>

         <input type="hidden" id="geneidInput" value="0">
         <div class="control-group">
            <div class="controls">
               <a id="editGeneSave" class="btn btn-primary">Save</a>
               <a class="btn" data-dismiss="modal">Cancel</a>
            </div>
         </div>
      </fieldset>
   </form>
</div>
