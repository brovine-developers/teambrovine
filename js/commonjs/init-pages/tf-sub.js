$(window).load(function () {
   var Table = Brovine.table.Table;
   var schemas = Brovine.table.schema;
   var bread = Brovine.breadcrumb;
   var SelectAll = Brovine.selectAll;
   var RegFilter = Brovine.regInput.RegFilter;
   var LvalueFilter = Brovine.lvalueFilter;

   // Create all of the table instances
   var speciesList = new Table("#speciesList", schemas.species, "100px"),
       comparisonList = new Table("#comparisonList", schemas.comparison, "100px"),
       experimentList = new Table("#experimentList", schemas.experiment, "100px"),
       geneList1 = new Table("#geneList1", schemas.gene, "150px"),
       geneList2 = new Table("#geneList2", schemas.gene, "150px"),
       factorList1 = new Table("#factorList1", schemas.factor, "150px"),
       factorList2 = new Table("#factorList2", schemas.factor, "150px"),
       subtractList = new Table("#subtractList", schemas.factor, "150px");
       
   // Set up regulation filters
   var regFilter1 = new RegFilter("#regFilter1", geneList1.dt, 4),
       regFilter2 = new RegFilter("#regFilter2", geneList2.dt, 4);

   // Initialize select all buttons
   SelectAll("#selectAllGenes1", geneList1);
   SelectAll("#selectAllGenes2", geneList2);

   // Register the update handlers for each table 
   bread.registerTableUpdate(speciesList,
     [experimentList, geneList1, geneList2, factorList1, factorList2], comparisonList);
   bread.registerTableUpdate(comparisonList,
     [geneList1, geneList2, factorList1, factorList2], experimentList);
   bread.registerTableUpdate(experimentList, [factorList1, factorList2], geneList1);
   bread.registerTableUpdate(experimentList, [factorList1, factorList2], geneList2);

   // Register the reg. sequence filter options
   LvalueFilter.init('#sequenceFilterOptions');

   // Register the subtraction list updater
   var updateSubtract = function () {
      var data = [];
      var data1 = factorList1.dt._('tr', {"filter":"applied"});
      var data2 = factorList2.dt._('tr', {"filter":"applied"});

      if (data1 && data2 && data1.length !== 0 && data2.length !== 0) {
         for (var i = 0; i < data1.length; i++) {
            var include = true;

            for (var j = 0; j < data2.length; j++) {
               if (data1[i].transfac === data2[j].transfac) {
                  include = false;
                  break;
               }
            }

            if (include) {
               data.push(data1[i]);
            }
         }

         subtractList.dt.fnClearTable();
         subtractList.dt.fnAddData(data);
      }
   };

   bread.registerTableUpdate(geneList1, [], factorList1, updateSubtract);
   bread.registerTableUpdate(geneList2, [], factorList2, updateSubtract);
   bread.register('#sequenceFilterOptions', function () {
      geneList1.updateCrumb();
      bread.emit(geneList1);
      geneList2.updateCrumb();
      bread.emit(geneList2);
   });

   var depList = [
      [speciesList],
      [comparisonList],
      [experimentList],
      [geneList1, geneList2],
      [factorList1, factorList2]
   ];

   // Get the initial data for the species list
   jQuery.get("ajax/getSpeciesList",
      {},
      function (data) {
         speciesList.dt.fnClearTable();
         speciesList.dt.fnAddData(data);
      },
      'json'
   );
});
