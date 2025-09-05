<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
	public function index(Request $request)
	{
		$q = Log::query()->with('user')->latest('data_hora');

		if ($m = $request->get('modulo'))   $q->where('modulo', $m);
		if ($a = $request->get('alteracao'))$q->where('alteracao', $a);
		if ($u = $request->get('user_id'))  $q->where('user_id', $u);
		if ($d = $request->get('dia'))      $q->whereDate('data_hora', $d);

		$logs = $q->paginate(20)->withQueryString();

		return view('admin.logs.index', compact('logs'));
	}
}
