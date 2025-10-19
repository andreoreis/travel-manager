<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTravelRequest;
use App\Http\Resources\BaseResource;
use App\Models\TravelRequest;
use App\Repositories\TravelRequestRepository;
use App\Services\TravelRequestService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TravelRequestController
{
    use AuthorizesRequests;

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

    public function show(TravelRequest $travelRequest)
    {
        $this->authorize('view', $travelRequest);
        return new BaseResource($travelRequest);
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

    public function cancel(TravelRequest $travelRequest)
    {
        $this->authorize('cancel', $travelRequest);
        return $this->service->cancel($travelRequest);
    }
}
