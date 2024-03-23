<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $companyQuery = Company::with(['user'])->whereHas('user', function ($query) {
            $query->where('user_id', Auth::id());
        });

        // kiwkiw.com/api/company?id=1
        // get single data
        if($id) {
            $company = $companyQuery->find($id);

            if($company) {
                return ResponseFormatter::success($company, 'Company Found');
            }

            return ResponseFormatter::error('Company not found', 404);
        }

        // kiwkiw.com/api/company
        // get multiple data
        $companies = $companyQuery;

        // kiwkiw.com/api/company?name=PTErajaya
        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit), 'Companies found'
        );
    }

    public function create(CreateCompanyRequest $request)
    {
        try {
            // upload logo
            if($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // create company
            $company = Company::create([
                'name' => $request->name,
                'logo' => isset($path) ? $path : ''
            ]);

            if(!$company) {
                throw new Exception('Company Not Created');
            }

            // attach company to user (many to many)
            $user = User::find(Auth::id());
            $user->company()->attach($company->id);

            // load users to company
            $company->load('user');

            return ResponseFormatter::success($company, 'Company Created');
        } catch (Exception $e) {
            return ResponseFormatter::error('Failed Create Company', 500);
        }
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        try {
            // find company id
            $company = Company::find($id);

            if(!$company) {
                throw new Exception('Company Not Found');
            }

            // upload logo
            if($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // update company
            $company->update([
                'name' => $request->name,
                'logo' => isset($path) ? $path : $company->logo,
            ]);

            return ResponseFormatter::success($company, 'Company Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error('Failed Update Company', 500);
        }
    }
}
