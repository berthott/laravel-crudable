<?php

namespace berthott\Crudable\Http\Controllers;

use berthott\Crudable\Facades\CrudQuery;
use berthott\Crudable\Facades\CrudRelations;
use berthott\Crudable\Http\Requests\UpdateRequest;
use berthott\Crudable\Models\Contracts\Targetable;
use berthott\Crudable\Models\Traits\Targetable as TraitsTargetable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

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
        return CrudQuery::getQuery($this->target);
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
        $validated = $request->validated();
        return CrudRelations::attach($this->target::create($validated), $validated);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, mixed $id): Model
    {
        $instance = $this->target::findOrFail($id);
        $validated = $request->validated();
        $instance->update($validated);
        return CrudRelations::attach($instance, $validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(mixed $id): int
    {
        $ret = $this->target::destroy($id);
        CrudRelations::deleteUnrelatedCreatables($this->target);
        return $ret;
    }
}
