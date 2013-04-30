<div class="row">
   <div class="span12">
      <h2>Transcription Factor Subtract</h2>
   </div>
</div>

<!-- 1st row of tables: Species, comparison, and experiment. -->
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

<!-- Regulation filtering row -->
<div class="row">
   <div class="span2 filter-label"><h4>Filter by Regulation: </h4></div>
   <div class="span4">
         <input type="text" id="regFilter1" placeholder="Search for Regulations" />
         <br />
   </div>

   <div class="span2 filter-label"><h4>Filter by Regulation: </h4></div>
   <div class="span4">
         <input type="text" id="regFilter2" placeholder="Search for Regulations" />
         <br />
   </div>
</div>

<!-- 2nd row of tables: Gene list. -->
<div class="row">
   <div class="span6">
      <table class="table table-striped table-bordered geneList" id="geneList1">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>

   <div class="span6">
      <table class="table table-striped table-bordered geneList" id="geneList2">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
</div>
&nbsp;
<div class="row ruleRow">
   <div class="span6">
      <a class="btn btn-info" id="selectAllGenes1">
         <i class="icon-tags icon-white"></i>Select All
      </a>
      <a class="btn" id="geneExport1"><i class="icon-download"></i>Export Data</a>
   </div>

   <div class="span6">
      <a class="btn btn-info" id="selectAllGenes2">
         <i class="icon-tags icon-white"></i>Select All
      </a>
      <a class="btn" id="geneExport2"><i class="icon-download"></i>Export Data</a>
   </div>
</div>

<!-- 3rd row: Factor lists (we hide this for TF subtraction) -->
<div class="row hide">
   <div class="span6">
      <table class="table table-striped table-bordered" id="factorList1">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>

   <div class="span6">
      <table class="table table-striped table-bordered" id="factorList2">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
</div>

<div class="row ruleRow hide">
   <div class="span6">
      <a class="btn" id="factorExport1"><i class="icon-download"></i>Export Data</a>
   </div>

   <div class="span6">
      <a class="btn" id="factorExport2"><i class="icon-download"></i>Export Data</a>
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
               <label>Min La<br />
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
               <div class="row" id="sense">
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

<!-- 4th row: Factor Subtraction list -->
<div class="row">
  <div class="span2">&nbsp;</div>

  <div class="span8">
    <table class="table table-striped table-bordered" id="subtractList">
      <thead></thead>
      <tbody></tbody>
    </table>
  </div>

  <div class="span2">&nbsp;</div>
</div>
&nbsp;
<div class="row ruleRow">
  <div class="span2">&nbsp;</div>

   <div class="span8">
      <a class="btn" id="subtractExport"><i class="icon-download"></i>Export Data</a>
   </div>

  <div class="span2">&nbsp;</div>
</div>

<div class="row hide">
   <div class="span12">
      <table class="table table-striped table-bordered" id="sequenceList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
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
   </div>
</div>
