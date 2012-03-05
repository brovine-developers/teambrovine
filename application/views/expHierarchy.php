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
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="speciesList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
   <div class="span4 selector">
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="comparisonList">
         <thead>
            <th>Comparisons</th>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
   <div class="span6 selector">
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="experimentList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
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
<div class="row ruleRow">
   <div class="span8">
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="geneList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
   <div class="span4">
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="factorList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
</div>
<div class="row">
   <div class="span12">
      <div class="well">
         <h3>Regulatory Sequence Filter Options</h3>
         <div class="row">
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
               <div class="row senseFilters">
                  <div class="span1">
                  <label class="radio">
                     <input type="radio" name="senseFilter" value="all" checked>
                     All
                  </label>
                  </div>
                  <div class="span1">
                  <label class="radio">
                     <input type="radio" name="senseFilter" value="n">
                     N
                  </label>
                  </div>
                  <div class="span1">
                  <label class="radio">
                     <input type="radio" name="senseFilter" value="r">
                     R
                  </label>
                  </div>
               </div>
            </div>
         </div>
      </div>
      
   </div>
</div>
<div class="row ruleRow">
   <div class="span12">
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="sequenceList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
</div>
<div class="row" id="sequenceInfo">
</div>
