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
       geneList1 = new Table("#geneList1", schemas.geneInclude, "150px"),
       geneList2 = new Table("#geneList2", schemas.geneExclude, "150px"),
       subtractList = new Table("#subtractList", schemas.factor, "150px");

   // Set up regulation filters
   var regFilter1 = new RegFilter("#regFilter1", geneList1.dt, 4),
       regFilter2 = new RegFilter("#regFilter2", geneList2.dt, 4);

   // Initialize select all buttons
   SelectAll("#selectAllGenes1", geneList1);
   SelectAll("#selectAllGenes2", geneList2);

   // Register the update handlers for each table
   // bread.registerTableUpdate(fromTable, [tables to clear], toTable);
   bread.registerTableUpdate(speciesList,
     [experimentList, geneList1, geneList2, subtractList], comparisonList);
   bread.registerTableUpdate(comparisonList,
     [geneList1, geneList2, subtractList], experimentList);
   bread.registerTableUpdate(experimentList, [subtractList], geneList1);
   bread.registerTableUpdate(experimentList, [subtractList], geneList2);

   // Register the reg. sequence filter options
   LvalueFilter.init('#sequenceFilterOptions');

   // Register some more update handlers, and a special handler to update the
   // gene lists when the l-value filter is changed.
   bread.registerTableUpdate(geneList1, [], subtractList);
   bread.registerTableUpdate(geneList2, [], subtractList);
   bread.register('#sequenceFilterOptions', function () {
      geneList2.updateCrumb();
      bread.emit(geneList2);
      geneList1.updateCrumb();
      bread.emit(geneList1);
   });

   // Future thoughts: make dependency registration as easy as a nested array.
   var depList = [
      [speciesList],
      [comparisonList],
      [experimentList],
      [geneList1, geneList2],
   ];

   // Set up local download buttons
   $("#geneExport1").localDownload({
      "func": geneList1.getCSVData.bind(geneList1),
      "filename": experimentList.getUniqueName.bind(experimentList, "label", "gene-data-")
   });

   $("#geneExport2").localDownload({
      "func": geneList2.getCSVData.bind(geneList2),
      "filename": experimentList.getUniqueName.bind(experimentList, "label", "gene-data-")
   });

   $("#subtractExport").localDownload({
      "func": subtractList.getCSVData.bind(subtractList),
      "filename": geneList1.getUniqueName.bind(geneList1, "geneabbrev", "factor-data-")
   });

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
