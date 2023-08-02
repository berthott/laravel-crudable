<?php

namespace berthott\Crudable\Http\Controllers;

use berthott\Crudable\Http\Requests\DeleteManyRequest;
use berthott\Crudable\Http\Requests\UpdateRequest;
use berthott\Scopeable\Facades\Scopeable;
use Facades\berthott\Crudable\Services\CrudableService;
use Facades\berthott\Crudable\Services\CrudAttributesService;
use Facades\berthott\Crudable\Services\CrudRelationsService;
use Facades\berthott\Crudable\Services\CrudQueryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Crudable API endpoint implementation.
 */
class CrudController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    private string $target;

    public function __construct()
    {
        $this->target = CrudableService::getTarget();
    }

    /**
     * Display a listing of the resource.
     * 
     * @api
     */
    public function index(): Collection
    {
        return Scopeable::filterScopes(CrudQueryService::getQuery($this->target));
    }

    /**
     * Display the specified resource.
     * 
     * @api
     */
    public function show(mixed $id): Model
    {
        return Scopeable::checkScopes(CrudAttributesService::get($this->target::findOrFail($id)));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @api
     */
    public function store(UpdateRequest $request): Model
    {
        $validated = $request->validated();

        return Scopeable::checkScopes(
            CrudAttributesService::get(CrudRelationsService::attach($this->target::create($validated), $validated)),
            function ($instance) {
                $instance->delete();
            }
        );
    }

    /**
     * Update the specified resource in storage.
     * 
     * @api
     */
    public function update(UpdateRequest $request, mixed $id): Model
    {
        $instance = $this->target::findOrFail($id);
        $validated = $request->validated();
        $backup = $instance->fresh();
        $instance->update($validated);

        return Scopeable::checkScopes(
            CrudAttributesService::get(CrudRelationsService::attach($instance, $validated)),
            function ($instance) use ($backup) {
                $instance->delete();
                $backup->save();
            }
        );
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @api
     */
    public function destroy(mixed $id): int
    {
        Scopeable::checkScopes($this->target::findOrFail($id));
        $ret = $this->target::destroy($id);
        CrudRelationsService::deleteUnrelatedCreatables($this->target);

        return $ret;
    }

    /**
     * Remove the specified resources from storage.
     * 
     * @api
     */
    public function destroy_many(DeleteManyRequest $request): int
    {
        foreach ($request->ids as $id) {
            Scopeable::checkScopes($this->target::findOrFail($id));
        }
        $ret = $this->target::destroy($request->ids);
        CrudRelationsService::deleteUnrelatedCreatables($this->target);

        return $ret;
    }

    /**
     * Display the schema data.
     * 
     * @api
     */
    public function schema(): array
    {
        return $this->target::schema();
    }
}
