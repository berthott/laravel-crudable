<?php

namespace berthott\Crudable\Http\Controllers;

use berthott\Crudable\Http\Requests\UpdateRequest;
use berthott\Crudable\Models\Contracts\Targetable;
use berthott\Crudable\Models\Traits\Targetable as TraitsTargetable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;

class CrudController implements Targetable
{
    use TraitsTargetable;
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): Collection
    {
        return $this->target::all();
    }

    /**
     * Display the specified resource.
     */
    public function show(mixed $id): Model
    {
        return $this->target::findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UpdateRequest $request): Model
    {
        return $this->target::create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, mixed $id): Model
    {
        $instance = $this->target::findOrFail($id);
        $instance->update($request->validated());

        return $instance;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(mixed $id): int
    {
        return $this->target::destroy($id);
    }
}
