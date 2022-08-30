<?php

namespace berthott\Crudable\Http\Controllers;

use berthott\Crudable\Exceptions\ForbiddenException;
use berthott\Crudable\Facades\CrudQuery;
use berthott\Crudable\Facades\CrudRelations;
use berthott\Crudable\Facades\Scopable;
use berthott\Crudable\Http\Requests\DeleteManyRequest;
use berthott\Crudable\Http\Requests\UpdateRequest;
use berthott\Crudable\Models\Contracts\Targetable;
use berthott\Crudable\Models\Traits\Targetable as TraitsTargetable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

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
        return Scopable::filterForScopes(CrudQuery::getQuery($this->target));
    }

    /**
     * Display the specified resource.
     */
    public function show(mixed $id): Model
    {
        $instance = $this->target::findOrFail($id);
        if (!Scopable::isAllowedInScopes($instance)) {
            throw new ForbiddenException;
        }
        return $instance;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UpdateRequest $request): Model
    {
        $validated = $request->validated();
        $instance = CrudRelations::attach($this->target::create($validated), $validated);

        if (!Scopable::isAllowedInScopes($instance)) {
            $instance->delete();
            throw new ForbiddenException;
        }
        return $instance;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, mixed $id): Model
    {
        $instance = $this->target::findOrFail($id);
        $validated = $request->validated();
        $backup = $instance->fresh();
        $instance->update($validated);
        $ret = CrudRelations::attach($instance, $validated);

        if (!Scopable::isAllowedInScopes($ret)) {
            $ret->delete();
            $backup->save();
            throw new ForbiddenException;
        }
        return $ret;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(mixed $id): int
    {
        if (!Scopable::isAllowedInScopes($this->target::findOrFail($id))) {
            throw new ForbiddenException;
        }
        $ret = $this->target::destroy($id);
        CrudRelations::deleteUnrelatedCreatables($this->target);

        return $ret;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy_many(DeleteManyRequest $request): int
    {
        foreach ($request->ids as $id) {
            if (!Scopable::isAllowedInScopes($this->target::findOrFail($id))) {
                throw new ForbiddenException;
            }
        }
        $ret = $this->target::destroy($request->ids);
        CrudRelations::deleteUnrelatedCreatables($this->target);

        return $ret;
    }

    /**
     * Display the schema data.
     */
    public function schema(): array
    {
        return $this->target::schema();
    }
}
