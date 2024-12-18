<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeeStoreRequest;
use App\Http\Requests\FeeUpdateRequest;
use App\Http\Resources\FeeResource;
use App\Interface\Service\FeeServiceInterface;
use App\Models\Fees;
use Illuminate\Foundation\Http\FormRequest;

class FeeController extends Controller
{
    private $feeService;

    public function __construct(FeeServiceInterface $feeService)
    {
        $this->feeService = $feeService;
    }

    public function index()
    {
        return $this->feeService->findFees();
    }

    public function indexActive()
    {
        $fees = Fees::where('isactive', 1)->get();

        return FeeResource::collection($fees);
    }

    public function store(FeeStoreRequest $request)
    {
        return $this->feeService->createFee($request);
    }

    public function show(int $id)
    {
        return $this->feeService->findFeeById($id);
    }

    public function update(FeeUpdateRequest $request, int $id)
    {
        return $this->feeService->updateFee($request, $id);
    }

    public function destroy(int $id)
    {
        return $this->feeService->deleteFee($id);
    }
}
