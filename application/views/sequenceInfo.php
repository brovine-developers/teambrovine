<div class="span12">
   <h2>Sequence Info</h2>
   <div class="row">
      <div class="span6">
         <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
            <tbody>
               <tr>
                  <th>Start</th>
                  <td><?=$sequenceInfo['beginning']?></td>
               </tr>
               <tr>
                  <th>Length</th>
                  <td><?=$sequenceInfo['length']?></td>
               </tr>
               <tr>
                  <th>Sense</th>
                  <td><?=$sequenceInfo['sense']?></td>
               </tr>
               <tr>
                  <th>Sequence</th>
                  <td><?=$sequenceInfo['sequence']?></td>
               </tr>
               <tr>
                  <th>Gene</th>
                  <td><?=$sequenceInfo['geneabbrev']?></td>
               </tr>
               <tr>
                  <th>Species</th>
                  <td><?=ucfirst($sequenceInfo['species'])?></td>
               </tr>
               <tr>
                  <th>Comparison</th>
                  <td><?=$sequenceInfo['celltype']?></td>
               </tr>
               <tr>
                  <th>Experiment</th>
                  <td><?=$sequenceInfo['label']?></td>
               </tr>
            </tbody>
         </table>
      </div>

      <div class="span6">
         <div class="row">
            <div class="span3">
               <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
                  <thead>
                     <th>Factors</th>
                  </thead>
                  <tbody>
                     <? foreach ($sequenceInfo['transfacs'] as $factor) : ?>
                        <tr><td><?=$factor?></td></tr>
                     <? endforeach; ?>
                  </tbody>
               </table>
            </div>
            <div class="span3">
               <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
                  <thead>
                     <th>Studies</th>
                  </thead>
                  <tbody>
                     <? foreach ($sequenceInfo['studies'] as $study) : ?>
                        <tr><td><?=$study?></td></tr>
                     <? endforeach; ?>
                  </tbody>
               </table>
            </div>
         </div>

         <h3>Similar Sequences</h3>
         <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
            <thead>
               <th>Begin</th>
               <th>Length</th>
               <th>Sense</th>
               <th>Sequence</th>
            </thead>
            <tbody>
               <? if (count($sequenceInfo['similar']) > 0) : ?>
                  <? foreach ($sequenceInfo['similar'] as $similar) : ?>
                     <tr>
                        <td><?=$similar['beginning']?></td>
                        <td><?=$similar['length']?></td>
                        <td><?=$similar['sense']?></td>
                        <td><?=$similar['sequence']?></td>
                     </tr>
                  <? endforeach; ?>
               <? else : ?>
                  <tr><td colspan="4">None Found</td></tr>
               <? endif; ?>
            </tbody>
         </table>
      </div>
      
   </div>
</div>

