<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\BaseService;

/**
 * This is the newer version of DBModel.
 *
 * This DBModel use a more powerful query syntax(SQL 92 of MySQL Flavor).
 *
 * You can search any database using the syntax of MySQL flavor, and this model will translate
 * your sql to the destination's SQL syntax by:
 *
 * 1. Parsing the SQL to a syntax tree
 * 2. Filter the tree using the query filters you have set through the query_filters configuration
 * 3. Translate the syntax tree through the translator(SQLCreator you have configured for this model,
 * you can sure to configure SQLCreator of any model you created, by using the configuration, though
 * you can set the SQLCreator by code to your model, but that's not suggested.
 *
 * This Model trying its best to support the old DBModel's method.
 *
 *
 * @author Jack
 * @version 1.1
 * @date Tue Jun 16 13:06:15 2015
 *
 * @Clips\Library("sqlParser")
 */
class DBModelV2 extends BaseService {
}
