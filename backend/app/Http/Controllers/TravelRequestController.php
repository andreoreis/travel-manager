<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTravelRequest;
use App\Http\Resources\BaseResource;
use App\Models\TravelRequest;
use App\Repositories\TravelRequestRepository;
use App\Services\TravelRequestService;
use Illuminate\Http\Request;

class TravelRequestController extends Controller
{
    private $service;
    private $repository;

    public function __construct(TravelRequestService $service, TravelRequestRepository $repository) {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
       return $this->service->getListTravel($request);
    }

    public function store(StoreTravelRequest $request)
    {
        return $this->service->saveTravel($request);
    }

    public function show(int $id)
    {
        $travel = $this->repository::find($id);
        return new BaseResource($travel);
    }

    public function update(StoreTravelRequest $request, TravelRequest $travelRequest)
    {
        return $this->service->update($request, $travelRequest);
    }

    public function destroy(TravelRequest $travelRequest)
    {
        return $this->service->destroy($travelRequest);
    }

    public function changeStatus(Request $request, TravelRequest $travelRequest)
    {
        return $this->service->changeStatus($request, $travelRequest);
    }
}
