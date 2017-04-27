<?php namespace Questionare\Http\Controllers;

use Questionare\Http\Controllers\Controller;

class DomainsController extends Controller {

	public function getDomains($count, $start) {
		return Domain::skip($start)->take($count)->get()->toJson();
	}
}