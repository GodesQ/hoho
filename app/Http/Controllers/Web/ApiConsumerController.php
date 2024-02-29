<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\APIConsumer\StoreRequest;
use App\Models\ApiConsumer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ApiConsumerController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()) {
            $data = ApiConsumer::all();
            return DataTables::of($data)
                    ->addColumn("status", function ($row) {
                        if ($row->status) {
                            return '<div class="badge bg-label-success">Active</div>';
                        } else {
                            return '<div class="badge bg-label-warning">InActive</div>';
                        }
                    })
                    ->addColumn("actions", function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/api-consumers/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions', 'status'])
                    ->make(true);
        }
        return view('admin-page.api_consumers.list-api-consumer');
    }

    public function create(Request $request) {
        return view('admin-page.api_consumers.create-api-consumer');
    }

    public function store(StoreRequest $request) {
        $data = $request->validated();
        $api_code = $this->generateAPICode($request->consumer_name);
        $api_key = $this->generateAPIKey();

        $consumer = ApiConsumer::create(array_merge($data, [
            'api_code' => $api_code,
            'api_key' => $api_key
        ]));

        return redirect()->route('admin.api_consumers.edit', $consumer->id)->withSuccess('Consumer added successfully');
    }

    public function edit(Request $request, $id) {
        $consumer = ApiConsumer::findOrFail($id);
        return view('admin-page.api_consumers.edit-api-consumer', compact('consumer'));
    }

    public function update(StoreRequest $request, $id) {
        $data = $request->validated();
        $api_code = $this->generateAPICode($request->consumer_name);
        $consumer = ApiConsumer::findOrFail($id);
        
        $consumer->update($data);

        return back()->withSuccess('Consumer updated successfully');
    }

    public function destroy(Request $request) {

    }


    #HELPERS 
    private function generateAPICode($consumer_name) {
        return 'hoho-code-' . Str::lower(str_replace(' ', '', $consumer_name)) . date('mdy');
    }

    private function generateAPIKey() {
        return 'hoho-key' . Str::random(25) . date('mdy');
    }
}
