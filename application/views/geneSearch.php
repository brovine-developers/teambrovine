<div class="row">
   <div class="span12">
      <h1>Gene Search</h1>
   </div>
</div>

<div class="row">
   <div class ="span12">
      <h3>Search Scope</h3>
   </div>
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
 <div class ="span12">
      <h6>Ctrl+Click or AppleKey+Click to add multiple Transfacs.</h6>
   </div>
   <div class="span6">
      <table class="table table-striped table-bordered" id="factorList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
   
   <div class="span6">
      <form class="well form-inline">
         <fieldset>
            <legend>Filter Options</legend>
         </fieldset>
         <fieldset class="ruleTop">
            <div class="row pad">
               <div class="span2-5 filter">
                  <label class="control-label">Min La: &nbsp;</label>
                  <span class="begin"></span>
                  <input type="text" class="filter span1-5" id="minla" placeholder="Loading..." disabled />
                  <span class="end"></span>
               </div>
               
               <div class="span2-5 filter">
                  <label>Min La/: &nbsp;</label>
                  <span class="begin"></span>
                  <input type="text" class="filter span1-5" id="minlaslash" placeholder="Loading..." disabled />
                  <span class="end"></span>
               </div>
            </div>
               
            <div class="row pad">
               <div class="span2-5 filter">
                  <label>Min Lq: &nbsp;</label>
                  <span class="begin"></span>
                  <input type="text" class="filter span1-5" id="minlq" placeholder="Loading..." disabled />
                  <span class="end"></span>
               </div>
               
               <div class="span2-5 filter">
                  <label>Max Ld: &nbsp;</label>
                  <span class="begin"></span>
                  <input type="text" class="filter span1-5" id="maxld" placeholder="Loading..." disabled />
                  <span class="end"></span>
               </div>
            </div>
         </fieldset>
      </form>
   </div>
</div>

<div class="row">
   <div class="span2 filter-label"><h4>Filter by Regulation: </h4></div>
   <div class="span6">
         <input type="text" id="regFilter" placeholder="Search for Regulations" />
         <br />
   </div>
</div>

<div class="row ruleRow">
   <div class="span12">
      <h3>Found Genes</h3>
   </div>
   <div class="span6">
      <table class="table table-striped table-bordered" id="geneFoundList">
         <thead>
         </thead>
         <tbody>
         </tbod>
      </table>
   </div>
   <div class="span6">
      <table class="table table-striped table-bordered" id="comparisonFromGeneList">
         <thead>
         </thead>
         <tbody>
         </tbody>
      </table>
   </div>
</div>

