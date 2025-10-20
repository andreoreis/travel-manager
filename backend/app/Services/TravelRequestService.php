<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TravelRequestStatus;
use App\Exceptions\ApiException;
use App\Http\Requests\StoreTravelRequest;
use App\Models\TravelRequest;
use App\Notifications\TravelRequestStatusChanged;
use App\Repositories\TravelRequestRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class TravelRequestService
{
    private $travel_request_repository;

    public function __construct(TravelRequestRepository $travel_request_repository) {
        $this->travel_request_repository = $travel_request_repository;
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

        $users = $this->travel_request_repository->queryWithFilters($filters)->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Cria um novo pedido de viagem
     */
    public function saveTravel(StoreTravelRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $travelRequest = $this->travel_request_repository::create($data);

        return response()->json($travelRequest, Response::HTTP_CREATED);
    }

    /**
     * Atualiza um pedido de viagem
     */
    public function update(StoreTravelRequest $request, TravelRequest $travelRequest)
    {
        $data = $request->validated();
        $travelUpdated = $this->travel_request_repository::update($travelRequest->id, $data);

        return response()->json($travelUpdated);
    }

    /**
     * Apaga um pedido de viagem
     */
    public function destroy(TravelRequest $travelRequest)
    {
        $this->travel_request_repository::delete($travelRequest->id);
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

        // Captura o status anterior
        $oldStatus = $travelRequest->status->value;

        if ($oldStatus === 'aprovado') {
            throw new ApiException('Não é possível cancelar um pedido aprovado.', Response::HTTP_FORBIDDEN);
        }

        // Atualiza o status
        $newStatus = $request->input('status');
        $travelRequest->status = $newStatus;
        $travelRequest->save();



        // Envia notificação
        if ($travelRequest->user) {
            $travelRequest->user->notify(
                new TravelRequestStatusChanged($travelRequest, $oldStatus, $newStatus)
            );
        }

        return response()->json($travelRequest);
    }
}
