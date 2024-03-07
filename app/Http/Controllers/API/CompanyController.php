<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // kiwkiw.com/api/company?id=1
        if($id) {
            $company = Company::with(['user'])->find($id);

            if($company) {
                return ResponseFormatter::success($company, 'Company Found');
            }

            return ResponseFormatter::error('Company not found', 404);
        }

        // kiwkiw.com/api/company
        $companies = Company::with(['user']);

        // kiwkiw.com/api/company?name=PTErajaya
        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit), 'Companies found'
        );
    }
}
