<h1>Frequent Itemset Generator</h1>

<p>The frequent itemset generator is a Java service that finds statistically significant sets of frequent items in
large datasets. For
example, let's say we have a database of supermarket transactions, or baskets,
each containing a number of grocery items. If we want to find the frequent
grocery items in this database, we're finding the grocery items which are most
frequently purchased together: the "frequent itemsets."</p>

<p>Generally we want the sets of items to occur in at least m transactions. We'll
call this the minimum support number. The minimum support number allows us to 
specify how frequent an itemset must be to be included in the final output -
obviously, a set of items that does not occur in any transactions, or even a
small number of transactions, is insignificant to the user of the system. The
minimum support generally must be tuned for each specific dataset to determine
what will give the user plenty of data, but not so much data that the output
becomes unusable.</p>

<p>To this end, we also include the maximum support number, which caps the number
of possible transactions any set in the final output can have. Imagine that at
a specific supermarket, nearly every customer buys milk when they buy anything
else. Then, it is not very significant to include milk in the final output,
since the item is so commonly purchased. Thus we can use the maximum support
to filter out items like this. As with the min support, this number will also
vary wildly between datasets. If you end up using the max support metric (less
than 100% of course) for a dataset,
this indicates that you should probably use the FPGrowth algorithm, as this
algorithm runs much quicker on datasets with a large number of similar items
between transactions.</p>

<h2>Use</h2>

This service was built to find the genetic transcription factors (proteins)
which most frequently occur together in one gene. However, you can add your own
dataset if you want to use it for a different purpose.

<h2>Customizability</h2>
<ul>
   <li>class BasketIterator: Implement BasketIterator to create your own dataset.</li>
   <li>class ItemsetGenerator: Implement ItemsetGenerator to create your own.</li>
 algorithm.
   <li>file lib/passwd.groovy: Password file - holds database configuration info.</li>
</ul>

<h2>API Reference</h2>
<ul>
   <li><strong>Get request:</strong> <code>get [minSup:decimal:0-1] [maxSup:decimal:0-1]</code><br />
 <p>Returns a list of the itemsets between the min and max support values.
 <code>time</code> is always 0 on FAILURE.
 <code>maxSup</code> must be less than <code>minSup</code>, obviously. Both are decimal values
 indicating the percent support an itemset must have to be included.</p>
 <strong>Returns:</strong> 
 <pre>
 {
    'res': "(SUCCESS | FAILURE)":string,
    'item-cnt': "integer indicating the number of unique items":integer,
    'reason': "Reason for failure if FAILURE, request type if SUCCESS.":string,
    'message': "Explanation of failure iff 'res' == 'FAILURE'.":string,
    'time': "integer indicating the time it took to find itemsets":integer,
    'data': "map with the itemsets and their frequency counts; ex: [[one, two]: 138]":map
 }
</pre>
   </li>

   <li><strong>Set request:</strong> <code>set [BasketIterator] [ItemsetGenerator]</code>
   <p>Changes the algorithm and the dataset used when computing frequent item sets.</p>
   <p>If the request is successful, <code>res</code> will be set to SUCCESS and
<code>reason</code> will be "SET". Any subsequent queries by client will use the
 BasketIterator and ItemsetGenerator specified.<br />

 Both Set values must be fully-qualified class names.</p>
 <strong>Returns:</strong> 
 <pre>
 {
    'res': "(SUCCESS | FAILURE)":string,
    'reason': "Reason for failure if FAILURE, request type if SUCCESS.":string,
    'message': "Explanation of failure iff 'res' == 'FAILURE'.":string,
    'time': 0,
 }
</pre>
 </li>
</ul>

<h3>Two algorithms are available; selectable at runtime:</h3>
<ul>
   <li><strong>Apriori algorithm:</strong> Iterates through every transaction (supermarket basket)
 and candidate to find the most frequent sets of items. A candidate is any one
 subset of items in the entire set of items (in our example, every item in the
 supermarket). So we start with candidates with one item (Bread, milk, cheese),
 then we look for baskets with two items ([Bread, milk], [milk, cheese],
 [cheese, bread]), and so on until we've searched every subset. At worst case,
 this algorithm is exponential, as there are 2^n subsets for n items. But the
 trick is that we drop from consideration all larger subsets containing an
 infrequent item. For example, if we know that bread is in m - 1 baskets (one
 less than our minimum support) then we know bread is _never_ a frequent item,
 so we can drop [Bread, milk], [cheese, Bread], and [bread, milk, cheese] from
 our consideration. This optimization significantly speeds up the algorithm for
 reasonable minimum support values (if you set the minimum support to 0, every
 item set will be frequent and you'll have to iterate through every set).</li>

   <li><strong>FPGrowth algorithm:</strong> Based on the FPTree data structure. This algorithm builds
 a tree representing your dataset. The more items each transaction has in
 common, the smaller the tree will be and thus the faster this algorithm will
 procese all frequent itemsets.</li>
</ul>
