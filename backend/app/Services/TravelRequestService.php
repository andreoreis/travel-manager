<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TravelRequestStatus;
use App\Http\Requests\StoreTravelRequest;
use App\Models\TravelRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TravelRequestService
{
    private $user_repository;

    public function __construct(UserRepository $user_repository,) {
        $this->user_repository = $user_repository;
    }

    /**
     * Retorna uma lista paginada de pedidos de viagem
     */
    public function getListTravel(Request $request)
    {
        $filters = [
            'status' => $request->input('status'),
            'user_id' => $request->input('user_id'),
        ];

        $perPage = (int) $request->input('per_page', 15);

        $users = $this->user_repository->queryWithFilters($filters)->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Cria um novo pedido de viagem
     */
    public function saveTravel(StoreTravelRequest $request)
    {
        $data = $request->validated();

        $travelRequest = TravelRequest::create($data);

        return response()->json($travelRequest, 201);
    }

    /**
     * Atualiza um pedido de viagem
     */
    public function update(StoreTravelRequest $request, TravelRequest $travelRequest)
    {
        $data = $request->validated();
        $travelRequest->update($data);

        return response()->json($travelRequest);
    }

    /**
     * Apaga um pedido de viagem
     */
    public function destroy(TravelRequest $travelRequest)
    {
        $travelRequest->delete();
        return response()->noContent();
    }

    /**
     * Altera o status de um pedido de viagem
     */
    public function changeStatus(Request $request, TravelRequest $travelRequest)
    {
        $request->validate([
            'status' => ['required', Rule::in(TravelRequestStatus::values())],
        ]);

        $travelRequest->status = $request->input('status');
        $travelRequest->save();

        return response()->json($travelRequest);
    }
}
