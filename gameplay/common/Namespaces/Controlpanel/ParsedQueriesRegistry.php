<?php

namespace Controlpanel;

class ParsedQueriesRegistry extends QueriesRegistry {

	protected $itemClass = "\Controlpanel\ParsedQueries";
	protected $tableList = "st_parsedqueries AS st_queries";
	protected $registryTitle = "Parsed queries";

}