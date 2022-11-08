<?php

namespace berthott\Crudable\Http\Controllers;

use berthott\Crudable\Facades\CrudQuery;
use berthott\Crudable\Facades\CrudRelations;
use berthott\Crudable\Http\Requests\DeleteManyRequest;
use berthott\Crudable\Http\Requests\UpdateRequest;
use berthott\Crudable\Models\Contracts\Targetable;
use berthott\Crudable\Models\Traits\Targetable as TraitsTargetable;
use berthott\Scopeable\Facades\Scopeable;
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
        return Scopeable::filterScopes(CrudQuery::getQuery($this->target));
    }

    /**
     * Display the specified resource.
     */
    public function show(mixed $id): Model
    {
        return Scopeable::checkScopes($this->target::findOrFail($id)->load($this->target::showRelations()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UpdateRequest $request): Model
    {
        $validated = $request->validated();

        return Scopeable::checkScopes(
            CrudRelations::attach($this->target::create($validated), $validated)->load($this->target::showRelations()),
            function ($instance) {
                $instance->delete();
            }
        );
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

        return Scopeable::checkScopes(
            CrudRelations::attach(CrudRelations::attach($instance, $validated), $validated)->load($this->target::showRelations()),
            function ($instance) use ($backup) {
                $instance->delete();
                $backup->save();
            }
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(mixed $id): int
    {
        Scopeable::checkScopes($this->target::findOrFail($id));
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
            Scopeable::checkScopes($this->target::findOrFail($id));
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
