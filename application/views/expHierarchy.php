<div class="row">
   <div class="span12">
      <h1>Experiment Hierarchy</h1>
      <p>
         Begin by selecting a species. Yellow rows have been edited, and red rows have been hidden.
      </p>
      <p>
         <form action="ExperimentHierarchy" method="get" id="showHiddenForm">
            <label class="checkbox">
               <input type="checkbox" id="showHidden" name="showHidden" value="1" <?=$showHidden?>>
               Show Hidden Entries 
            </label>
         </form>
      </p>
   </div>
</div>

<div class="row">
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
<div class="row ruleRow">
   <div class="span4 offset2">
      <a class="btn btn-warning disabled" id="editComparison"><i class="icon-pencil icon-white"></i> Edit Comparison</a>
      <a class="btn btn-danger disabled hideButton" id="hideComparison"><i class="icon-minus-sign icon-white"></i><span> Hide Comparison</span></a>
   </div>
   <div class="span6">
      <a class="btn btn-warning disabled" id="editExperiment"><i class="icon-pencil icon-white"></i> Edit Experiment</a>
      <a class="btn btn-danger disabled hideButton" id="hideExperiment"><i class="icon-minus-sign icon-white"></i><span> Hide Experiment</span></a>
   </div>
</div>
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
      <a class="btn btn-danger disabled hideButton" id="hideGene"><i class="icon-minus-sign icon-white"></i><span> Hide Gene</span></a>
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
      <a class="btn btn-danger disabled hideButton" id="hideSequence"><i class="icon-minus-sign icon-white"></i><span> Hide Sequence</span></a>
   </div>
</div>
<div class="row hidden" id="sequenceInfo">
   <div class="span12">
      <div class="row">
         <div class="span6">
            <h2>Sequence Info</h2>
         </div>
         <div class="span6">
            <h3>Similar Sequences</h3>
         </div>
      </div>
      <div class="row">
         <div class="span6">
            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
               <tbody>
                  <tr>
                     <th>Start</th>
                     <td id="sequenceStart"></td>
                  </tr>
                  <tr>
                     <th>Length</th>
                     <td id="sequenceLength"></td>
                  </tr>
                  <tr>
                     <th>Sense</th>
                     <td id="sequenceSense"></td>
                  </tr>
                  <tr>
                     <th>Sequence</th>
                     <td id="sequenceSequence"></td>
                  </tr>
                  <tr>
                     <th>Gene</th>
                     <td id="sequenceGene"></td>
                  </tr>
                  <tr>
                     <th>Species</th>
                     <td id="sequenceSpecies"></td>
                  </tr>
                  <tr>
                     <th>Comparison</th>
                     <td id="sequenceComparison"></td>
                  </tr>
                  <tr>
                     <th>Experiment</th>
                     <td id="sequenceExperiment"></td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div class="span6">
            <table class="table table-striped table-bordered" id="similarList">
               <thead>
               </thead>
               <tbody>
               </tbody>
            </table>
         </div>
      </div>
      <table class="table table-striped table-bordered" id="matchList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
      <div class="row">
         <div class="span12">
            <a class="btn btn-warning disabled" id="editMatch"><i class="icon-pencil icon-white"></i> Edit Factor</a>
            <a class="btn btn-danger disabled hideButton" id="hideMatch"><i class="icon-minus-sign icon-white"></i><span> Hide Factor</span></a>
         </div>
      </div>
   </div>
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

<div class="modal hide fade" id="editComparisonModal">
   <form class="form-horizontal">
      <fieldset>
         <legend>Edit Comparison</legend>
         <div class="control-group">
            <label for="comparisonSpeciesInput" class="control-label">Species</label>
            <div class="controls">
               <input id="comparisonSpeciesInput">
            </div>
         </div>
         <div class="control-group">
            <label for="comparisonCelltypeInput" class="control-label">Comparison</label>
            <div class="controls">
               <input id="comparisonCelltypeInput">
            </div>
         </div>
         <div class="control-group">
            <label class="control-label">Last Edited</label>
            <div class="controls">
               <label style="padding-top: 5px" id="comparisonLastEdited"></label>
            </div>
         </div>

         <input type="hidden" id="comparisontypeidInput" value="0">
         <div class="control-group">
            <div class="controls">
               <a id="editComparisonSave" class="btn btn-primary">Save</a>
               <a class="btn" data-dismiss="modal">Cancel</a>
            </div>
         </div>
      </fieldset>
   </form>
</div>

<div class="modal hide fade" id="editExperimentModal">
   <form class="form-horizontal">
      <fieldset>
         <legend>Edit Experiment</legend>
         <div class="control-group">
            <label for="experimentLabelInput" class="control-label">Experiment</label>
            <div class="controls">
               <input id="experimentLabelInput">
            </div>
         </div>
         <div class="control-group">
            <label class="control-label">Last Edited</label>
            <div class="controls">
               <label style="padding-top: 5px" id="experimentLastEdited"></label>
            </div>
         </div>

         <input type="hidden" id="experimentidInput" value="0">
         <div class="control-group">
            <div class="controls">
               <a id="editExperimentSave" class="btn btn-primary">Save</a>
               <a class="btn" data-dismiss="modal">Cancel</a>
            </div>
         </div>
      </fieldset>
   </form>
</div>

<div class="modal hide fade" id="editSequenceModal">
   <form class="form-horizontal">
      <fieldset>
         <legend>Edit Sequence</legend>
         <div class="control-group">
            <label for="sequenceBeginningInput" class="control-label">Begin</label>
            <div class="controls">
               <input id="sequenceBeginningInput">
            </div>
         </div>
         <div class="control-group">
            <label for="sequenceLengthInput" class="control-label">Length</label>
            <div class="controls">
               <input id="sequenceLengthInput">
            </div>
         </div>
         <div class="control-group">
            <label for="sequenceSenseInput" class="control-label">Sense</label>
            <div class="controls">
               <input id="sequenceSenseInput">
            </div>
         </div>
         <div class="control-group">
            <label class="control-label">Last Edited</label>
            <div class="controls">
               <label style="padding-top: 5px" id="sequenceLastEdited"></label>
            </div>
         </div>

         <input type="hidden" id="seqidInput" value="0">
         <div class="control-group">
            <div class="controls">
               <a id="editSequenceSave" class="btn btn-primary">Save</a>
               <a class="btn" data-dismiss="modal">Cancel</a>
            </div>
         </div>
      </fieldset>
   </form>
</div>

<div class="modal hide fade" id="editMatchModal">
   <form class="form-horizontal">
      <fieldset>
         <legend>Edit Factor Info</legend>
         <div class="control-group">
            <label for="matchStudyInput" class="control-label">Study</label>
            <div class="controls">
               <input id="matchStudyInput">
            </div>
         </div>
         <div class="control-group">
            <label for="matchTransfacInput" class="control-label">Factor</label>
            <div class="controls">
               <input id="matchTransfacInput">
            </div>
         </div>
         <div class="control-group">
            <label for="matchLaInput" class="control-label">La</label>
            <div class="controls">
               <input id="matchLaInput">
            </div>
         </div>
         <div class="control-group">
            <label for="matchLaSlashInput" class="control-label">La/</label>
            <div class="controls">
               <input id="matchLaSlashInput">
            </div>
         </div>
         <div class="control-group">
            <label for="matchLqInput" class="control-label">Lq</label>
            <div class="controls">
               <input id="matchLqInput">
            </div>
         </div>
         <div class="control-group">
            <label for="matchLdInput" class="control-label">Ld</label>
            <div class="controls">
               <input id="matchLdInput">
            </div>
         </div>
         <div class="control-group">
            <label for="matchLpvInput" class="control-label">Lpv</label>
            <div class="controls">
               <input id="matchLpvInput">
            </div>
         </div>
         <div class="control-group">
            <label for="matchScInput" class="control-label">Sc</label>
            <div class="controls">
               <input id="matchScInput">
            </div>
         </div>
         <div class="control-group">
            <label for="matchSmInput" class="control-label">Sm</label>
            <div class="controls">
               <input id="matchSmInput">
            </div>
         </div>
         <div class="control-group">
            <label for="matchSpvInput" class="control-label">Spv</label>
            <div class="controls">
               <input id="matchSpvInput">
            </div>
         </div>
         <div class="control-group">
            <label for="matchPpvInput" class="control-label">Ppv</label>
            <div class="controls">
               <input id="matchPpvInput">
            </div>
         </div>
         <div class="control-group">
            <label class="control-label">Last Edited</label>
            <div class="controls">
               <label style="padding-top: 5px" id="matchLastEdited"></label>
            </div>
         </div>

         <input type="hidden" id="matchidInput" value="0">
         <div class="control-group">
            <div class="controls">
               <a id="editMatchSave" class="btn btn-primary">Save</a>
               <a class="btn" data-dismiss="modal">Cancel</a>
            </div>
         </div>
      </fieldset>
   </form>
</div>
